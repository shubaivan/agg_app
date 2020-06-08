<?php

namespace App\Repository;

use App\Cache\TagAwareQueryResultCacheBrand;
use App\Cache\TagAwareQueryResultCacheCategory;
use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\CategoryRelations;
use App\Entity\Product;
use App\Services\Helpers;
use App\Services\Models\CategoryService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Cache\Cache as ResultCacheDriver;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Cache\ResultCacheStatement;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Category[]|int getList(ResultCacheDriver $cache, QueryBuilder $qb, ParamFetcher $paramFetcher, bool $count = false)
 */
class CategoryRepository extends ServiceEntityRepository
{
    use PaginationRepository;
    const STRICT = 'strict';
    /**
     * @var Helpers
     */
    private $helpers;

    /**
     * @var TagAwareQueryResultCacheCategory
     */
    private $tagAwareQueryResultCacheCategory;

    /**
     * CategoryRepository constructor.
     * @param Helpers $helpers
     * @param TagAwareQueryResultCacheCategory $tagAwareQueryResultCacheCategory
     */
    public function __construct(
        ManagerRegistry $registry,
        Helpers $helpers,
        TagAwareQueryResultCacheCategory $tagAwareQueryResultCacheCategory
    )
    {
        parent::__construct($registry, Category::class);

        $this->helpers = $helpers;
        $this->tagAwareQueryResultCacheCategory = $tagAwareQueryResultCacheCategory;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return Category[]|int
     */
    public function getEntityList(
        ParamFetcher $paramFetcher,
        $count = false)
    {
        return $this->getList(
            $this->getEntityManager()->getConfiguration()->getResultCacheImpl(),
            $this->createQueryBuilder('s'),
            $paramFetcher,
            $count
        );
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return mixed
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function getCustomCategories(ParamFetcher $paramFetcher)
    {
//        $subYes = $this->getEntityManager()->createQueryBuilder();
//        $subYes
//            ->select("cr_y")
//            ->from(CategoryRelations::class,"cr_y")
//            ->innerJoin('cr_y.mainCategory', 'cr_ym')
//            ->where($subYes->expr()->eq('cr_ym.id', 'c.id'));
//
//        $subNot = $this->getEntityManager()->createQueryBuilder();
//        $subNot
//            ->select("cr_n")
//            ->from(CategoryRelations::class,"cr_n")
//            ->innerJoin('cr_y.subCategory', 'cr_nm')
//            ->where($subNot->expr()->eq('cr_nm.id', 'c.id'));
//
//        $qb = $this->createQueryBuilder('c');
//        $qb
//            ->select('c')
//            ->where($qb->expr()->exists($subYes->getDQL()))
//            ->andWhere($qb->expr()->not($subNot->getDQL()));
//
//
//        $query = $qb->getQuery();
//        $DQL = $query->getDQL();
//        $SQL = $query->getSQL();
//        $result = $query->getResult();
        $rs = $this->getMainSubCategoryIds();
        $mainCategoryIds = [];
        foreach ($rs as $id) {
            if (isset($id['id'])) {
                $mainCategoryIds[] = $id['id'];
            }
        }
        $parameterBag = new ParameterBag($paramFetcher->all());

        $limit = (int)$parameterBag->get('count');
        $offset = $limit * ((int)$parameterBag->get('page') - 1);
        $sortBy = $parameterBag->get('sort_by');
        $sortOrder = $parameterBag->get('sort_order');
        $sortBy = $this->getHelpers()->white_list($sortBy,
            ["id", "categoryName", "createdAt"], "Invalid field name " . $sortBy);

        $dql = '
                SELECT DISTINCT c
                FROM App\Entity\Category c    
                INNER JOIN c.mainCategoryRelations m                                       
                WHERE c.customeCategory = :custom
        ';

        if (is_array($mainCategoryIds) && count($mainCategoryIds) > 0) {
            $dql .= '
                            AND c.id IN (:ids)               
            ';
        }

        if ($parameterBag->get('search')) {
            $dql .= '
                AND ILIKE(c.categoryName, :search) = TRUE
            ';
        }
        $dql .= ' 
            ORDER BY c.' . $sortBy . ' ' . $sortOrder;

        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->enableResultCache()
            ->useQueryCache(true);

        if ($parameterBag->get('search')) {
            $query->setParameter(':search', '%' . $parameterBag->get('search') . '%');
        }
        if (is_array($mainCategoryIds) && count($mainCategoryIds) > 0) {
            $query->setParameter(':ids', $mainCategoryIds);
        }
        $query->setParameter(':custom', 't');

        return $query->getResult();
    }

    /**
     * @return mixed[]
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function getMainSubCategoryIds()
    {
        $connection = $this->getEntityManager()->getConnection();

        $query  = '
            SELECT id, category_name FROM category
            WHERE 
            EXISTS(SELECT 1 FROM category_relations WHERE main_category_id = category.id)
            AND
            NOT EXISTS(SELECT 1 FROM category_relations WHERE sub_category_id = category.id)
        ';
        $this->getTagAwareQueryResultCacheCategory()->setQueryCacheTags(
            $query,
            [],
            [],
            ['main_category_ids'],
            0,
            "main_category_ids"
        );
        [$query, $params, $types, $queryCacheProfile] = $this->getTagAwareQueryResultCacheCategory()
            ->prepareParamsForExecuteCacheQuery();

        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeCacheQuery(
            $query,
            $params,
            $types,
            $queryCacheProfile
        );

        $mainCategoryIds = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $statement->closeCursor();

        return $mainCategoryIds;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return int|mixed[]
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function fullTextSearchByParameterFetcher(ParamFetcher $paramFetcher, $count = false)
    {
        $parameterBag = new ParameterBag($paramFetcher->all());

        return $this->fullTextSearchByParameterBag($parameterBag, $count);
    }

    /**
     * @param Product|null $product
     * @param ParameterBag $parameterBag
     * @param int $depth
     * @param bool $explain
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function matchCategoryWithSub(
        ?Product $product = null, ParameterBag $parameterBag, int $depth = 3, $explain = false
    )
    {
        $connection = $this->getEntityManager()->getConnection();
        $mainSearch =
            $this->getHelpers()
                ->handleSearchValue($parameterBag->get(CategoryService::MAIN_SEARCH), false);

        if ($product && $product->getCategory()) {
            $checkMainCategoriesResult = $this->isMatchPlainCategoriesString(
                $product->getCategory(), $mainSearch
            );

            if (!$checkMainCategoriesResult) {
                return [];
            }
        }

        if ($depth > 1) {
            $subMainSearch =
                $this->getHelpers()
                    ->handleSearchValue($parameterBag->get(CategoryService::SUB_MAIN_SEARCH), $parameterBag->get(self::STRICT) === true);
        }

        if ($depth > 2) {
            $subSubMainSearch =
                $this->getHelpers()
                    ->handleSearchValue($parameterBag->get(CategoryService::SUB_SUB_MAIN_SEARCH), $parameterBag->get(self::STRICT) === true);
        }

        $query = '
            SELECT             
            DISTINCT ca.id';

        if ($explain === true) {
            $query .= '
                ,ca.category_name            
                ,cc.key_words AS main_keywords
                ,ts_rank_cd(to_tsvector(\'pg_catalog.swedish\',cc.key_words),to_tsquery(\'pg_catalog.swedish\', :main_search_parial_category)) AS  main_runk
            ';
        }

        if ($depth > 1) {
            $query .= '
                ,cr_main.sub_category_id AS sub_ctegory_id';
            if ($explain === true) {
                $query .= '
                    ,crsub.key_words AS sub_keywords
                    ,ts_rank_cd(to_tsvector(\'pg_catalog.swedish\',crsub.key_words),to_tsquery(\'pg_catalog.swedish\', :sub_main_search)) AS  sub_runk
                ';
            }
        }

        if ($depth > 2) {
            $query .= '
                ,cr_main_main.sub_category_id AS sub_sub_category_id';
            if ($explain === true) {
                $query .= '
                    ,crsub_main.key_words AS sub_sub_keywords
                    ,ts_rank_cd(to_tsvector(\'pg_catalog.swedish\',crsub_main.key_words),to_tsquery(\'pg_catalog.swedish\', :sub_sub_main_search)) AS  sub_sub__runk
                ';
            }
        }

        $query .= '
                FROM category as ca
            ';

        $query .= '
            INNER JOIN category_relations as cr_ca_main ON cr_ca_main.sub_category_id != ca.id
            INNER JOIN category_configurations as cc ON cc.category_id_id = ca.id
        ';
        if ($depth > 1) {
            $query .= '
                INNER JOIN category_relations as cr_main ON cr_main.main_category_id = ca.id
                INNER JOIN category_configurations as crsub ON crsub.category_id_id = cr_main.sub_category_id
            ';
        }

        if ($depth > 2) {
            $query .= '
                INNER JOIN category_relations as cr_main_main ON cr_main_main.main_category_id = cr_main.sub_category_id
                INNER JOIN category_configurations as crsub_main ON crsub_main.category_id_id = cr_main_main.sub_category_id
            ';
        }

        $query .= '
                WHERE to_tsvector(\'pg_catalog.swedish\',cc.key_words) @@ to_tsquery(\'pg_catalog.swedish\', :main_search_parial_category)
            ';


        if ($depth > 1) {
            $query .= '
                AND to_tsvector(\'pg_catalog.swedish\',crsub.key_words) @@ to_tsquery(\'pg_catalog.swedish\', :sub_main_search)
            ';
        }

        if ($depth > 2) {
            $query .= '
                AND to_tsvector(\'pg_catalog.swedish\',crsub_main.key_words) @@ to_tsquery(\'pg_catalog.swedish\', :sub_sub_main_search)
            ';
        }

        $query .= ' 
            ORDER BY
                ca.id';
        if ($explain === true) {
            $query .= '
                ,cc.key_words
            ';
        }

        if ($depth > 1) {
            $query .= '         
                    ,cr_main.sub_category_id';
            if ($explain === true) {
                $query .= '
                        ,crsub.key_words
                ';
            }
        }

        if ($depth > 2) {
            $query .= '                 
                    ,cr_main_main.sub_category_id';
            if ($explain === true) {
                $query .= '
                        ,crsub_main.key_words
                ';
            }
        }

        $params[':main_search_parial_category'] = $mainSearch;
        $types[':main_search_parial_category'] = \PDO::PARAM_STR;

        if ($depth > 1) {
            $params[':sub_main_search'] = $subMainSearch;
            $types[':sub_main_search'] = \PDO::PARAM_STR;
        }

        if ($depth > 2) {
            $params[':sub_sub_main_search'] = $subSubMainSearch;
            $types[':sub_sub_main_search'] = \PDO::PARAM_STR;
        }

        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeQuery(
            $query,
            $params,
            $types
        );

        $runkCategories = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $runkCategories;
    }

    /**
     * @param ParameterBag $parameterBag
     * @param bool $count
     * @return int|mixed[]
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function fullTextSearchByParameterBag(ParameterBag $parameterBag, $count = false)
    {
        $sort_by = isset($_REQUEST['sort_by']);
        $connection = $this->getEntityManager()->getConnection();
        $limit = (int)$parameterBag->get('count');
        $offset = $limit * ((int)$parameterBag->get('page') - 1);
        $sortBy = $parameterBag->get('sort_by');
        $sortOrder = $parameterBag->get('sort_order');

        $sortBy = $this->getHelpers()->white_list($sortBy,
            ["id", "categoryName", "createdAt"], "Invalid field name " . $sortBy);
        $sortOrder = $this->getHelpers()
            ->white_list(
                $sortOrder,
                [Criteria::DESC, Criteria::ASC],
                "Invalid ORDER BY direction " . $sortOrder
            );

        $searchField = $parameterBag->get('search');
        if ($searchField) {
            $search = $this->getHelpers()
                ->handleSearchValue($searchField, $parameterBag->get(self::STRICT) === true);
        } else {
            $search = $searchField;
        }
        $query = '';

        if ($count) {
            $query .= '
                        SELECT COUNT(DISTINCT category_alias.id)
            ';
        } else {
            $query .= '
                    SELECT                         
                            DISTINCT category_alias.id,
                            category_alias.category_name AS "categoryName",
                            category_alias.created_at AS "createdAt"
            ';

            if ($search) {
                $query .= '
                    ,ts_rank_cd(to_tsvector(\'pg_catalog.swedish\',category_alias.category_name), query_search) AS rank
                ';
            }
        }

        $query .= '
            FROM category category_alias 
        ';
        if ($search) {
            $query .= '
                JOIN to_tsquery(\'pg_catalog.swedish\', :search) query_search
                ON to_tsvector(\'pg_catalog.swedish\',category_alias.category_name) @@ query_search
            ';
        }

        if (!$count) {
            $query .= '
                    GROUP BY category_alias.id';
            if ($search) {
                $query .= ', query_search.query_search';
            }

            $query .=
                ($search ?
                    ($sort_by
                        ? ' ORDER BY rank DESC, ' . '"' . $sortBy . '"' . ' ' . $sortOrder . ''
                        : ' ORDER BY rank DESC')
                    : ' ORDER BY ' . '"' . $sortBy . '"' . ' ' . $sortOrder . '') . '
                                          
                    LIMIT :limit
                    OFFSET :offset;
            ';
        }

        $params = [];
        $types = [];

        if ($search) {
            $params[':search'] = $search;
            $types[':search'] = \PDO::PARAM_STR;
        }

        if (!$count) {
            $params[':offset'] = $offset;
            $params[':limit'] = $limit;
            $types[':offset'] = \PDO::PARAM_INT;
            $types[':limit'] = \PDO::PARAM_INT;
        }

        $this->getTagAwareQueryResultCacheCategory()->setQueryCacheTags(
            $query,
            $params,
            $types,
            ['category_full_text_search'],
            0, $count ? "category_search_cont" : "category_search_collection"
        );
        [$query, $params, $types, $queryCacheProfile] = $this->getTagAwareQueryResultCacheCategory()
            ->prepareParamsForExecuteCacheQuery();

        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeCacheQuery(
            $query,
            $params,
            $types,
            $queryCacheProfile
        );

        if ($count) {
            $shops = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $shops = isset($shops[0]['count']) ? (int)$shops[0]['count'] : 0;
        } else {
            $shops = $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
        $statement->closeCursor();

        return $shops;
    }

    /**
     * @param ParameterBag $parameterBag
     * @param string $query
     * @param array $params
     * @param array $types
     * @param bool $count
     * @return int|mixed[]
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function facetFilters(
        ParameterBag $parameterBag,
        string $query,
        array $params,
        array $types,
        bool $count = false
    )
    {
        $connection = $this->getEntityManager()->getConnection();
        $limit = (int)$parameterBag->get('count');
        $offset = $limit * ((int)$parameterBag->get('page') - 1);
        $sortBy = $parameterBag->get('sort_by');
        $sortOrder = $parameterBag->get('sort_order');

        $sortBy = $this->getHelpers()->white_list($sortBy,
            ["id", "categoryName", "createdAt"], "Invalid field name " . $sortBy);
        $sortOrder = $this->getHelpers()
            ->white_list(
                $sortOrder,
                [Criteria::DESC, Criteria::ASC],
                "Invalid ORDER BY direction " . $sortOrder
            );

        $searchField = $parameterBag->get('search');
        if ($searchField) {
            $search = $this->getHelpers()
                ->handleSearchValue($searchField, $parameterBag->get(self::STRICT) === true);
        } else {
            $search = $searchField;
        }

        if ($search) {
            if (!array_key_exists(':category_word', $params)) {
                $query .= '
                    WHERE category_alias.category_name ~ :category_word
                ';
            } else {
                $query .= '
                    AND category_alias.category_name ~ :add_category_word
                ';
            }
        }

        if (!$count) {
            $query .= '
                    GROUP BY category_alias.id';
            $query .=
                ' ORDER BY ' . '"' . $sortBy . '"' . ' ' . $sortOrder . '' . '                                          
                    LIMIT :limit
                    OFFSET :offset;
            ';

            $params = array_merge($params, [':offset' => $offset, ':limit' => $limit]);
            $types = array_merge($types, [':offset' => \PDO::PARAM_INT, ':limit' => \PDO::PARAM_INT]);
        }

        if ($search) {
            if (array_key_exists(':category_word', $params)) {
                $params[':add_category_word'] = $search;
            } else {
                $params[':category_word'] = $search;
                $params[':search_facet'] = $search;
                $types[':search_facet'] = \PDO::PARAM_STR;
            }
        }

        $this->getTagAwareQueryResultCacheCategory()->setQueryCacheTags(
            $query,
            $params,
            $types,
            ['category_facet_filter'],
            0, $count ? "category_facet_filter_cont" : "category_facet_filter_collection"
        );
        [$query, $params, $types, $queryCacheProfile] = $this->getTagAwareQueryResultCacheCategory()
            ->prepareParamsForExecuteCacheQuery();

        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeCacheQuery(
            $query,
            $params,
            $types,
            $queryCacheProfile
        );

        if ($count) {
            $categories = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $categories = isset($categories[0]['count']) ? (int)$categories[0]['count'] : 0;
        } else {
            $categories = $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
        $statement->closeCursor();

        return $categories;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return Category[]|int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCategoryByIds(ParamFetcher $paramFetcher, $count = false)
    {
        $ids = $paramFetcher->get('ids');
        if (is_array($ids)
            && array_search('0', $ids, true) === false) {
            $ids = array_filter($ids, function ($value, $key) {
                return boolval($value);
            }, ARRAY_FILTER_USE_BOTH);
            $qb = $this->createQueryBuilder('s');
            $qb
                ->where($qb->expr()->in('s.id', $ids));

            return $this->getList(
                $this->getEntityManager()->getConfiguration()->getResultCacheImpl(),
                $qb,
                $paramFetcher,
                $count
            );
        } else {
            throw new BadRequestHttpException($ids . ' not valid');
        }
    }

    /**
     * @return Helpers
     */
    public function getHelpers(): Helpers
    {
        return $this->helpers;
    }

    /**
     * @return TagAwareQueryResultCacheCategory
     */
    public function getTagAwareQueryResultCacheCategory(): TagAwareQueryResultCacheCategory
    {
        return $this->tagAwareQueryResultCacheCategory;
    }

    /**
     * @param string $productCategoriesData
     * @param string $mainCategoriesData
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function isMatchPlainCategoriesString(
        string $productCategoriesData,
        string $mainCategoriesData
    )
    {
        $connection = $this->getEntityManager()->getConnection();

        $query = 'select
        	to_tsvector(\'pg_catalog.swedish\',:product_categories_data) 
        	@@ to_tsquery(\'pg_catalog.swedish\', :main_categories_data) as match';

        $mainParams[':product_categories_data'] = $productCategoriesData;
        $mainType[':product_categories_data'] = \PDO::PARAM_STR;

        $mainParams[':main_categories_data'] = $mainCategoriesData;
        $mainType[':main_categories_data'] = \PDO::PARAM_STR;

        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeQuery(
            $query,
            $mainParams,
            $mainType
        );

        $isMatchResult = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (count($isMatchResult)) {
            $result = array_shift($isMatchResult);
            return $result['match'];
        } else {
            return false;
        }
    }

    /**
     * @param int $productId
     * @param $mainParams
     * @param $mainType
     * @param string $mainSearch
     * @param \Doctrine\DBAL\Connection $connection
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function checkAvailableAnalysisProduct(
        int $productId,
        string $mainSearch
    ): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $checkMainCategories = '
                SELECT pr.id
                FROM products pr
                WHERE to_tsvector(\'pg_catalog.swedish\',pr.category) @@ to_tsquery(\'pg_catalog.swedish\', :main_search)
                AND pr.id = :product_id
            ';
        $mainParams[':product_id'] = $productId;
        $mainType[':product_id'] = \PDO::PARAM_INT;

        $mainParams[':main_search'] = $mainSearch;
        $mainType[':main_search'] = \PDO::PARAM_STR;

        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeQuery(
            $checkMainCategories,
            $mainParams,
            $mainType
        );

        $checkMainCategoriesResult = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $checkMainCategoriesResult;
    }
}
