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
 * @method Category[]|int getList(ResultCacheDriver $cache, QueryBuilder $qb, ParamFetcher $paramFetcher, bool $count = false, string $cacheId = '')
 */
class CategoryRepository extends ServiceEntityRepository
{
    use PaginationRepository;
    const STRICT = 'strict';
    const MAIN_CATEGORY_IDS_DATA = 'main_category_ids_data';
    const CACHE_HOT_CATEGORY_ID = 'cache_hot_category_id';
    
    /**
     * @var Helpers
     */
    private $helpers;

    /**
     * @var TagAwareQueryResultCacheCategory
     */
    private $tagAwareQueryResultCacheCategory;

    /**
     * @var bool
     */
    private $checkMainCategoriesResult = false;

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
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder
            ->where('s.hotCategory = :hotCategory')
            ->setParameter('hotCategory', true);
        return $this->getList(
            $this->getEntityManager()->getConfiguration()->getResultCacheImpl(),
            $queryBuilder,
            $paramFetcher,
            $count,
            self::CACHE_HOT_CATEGORY_ID
        );
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function matchExistByName(string $name)
    {
        return $this->createQueryBuilder('c')
            ->where('c.categoryName = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return mixed
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function getCustomCategories(ParamFetcher $paramFetcher)
    {
        $rs = $this->getMainSubCategoryIds();
        if (!isset($rs['category_ids'])) {
            return [];
        }
        $mainCategoryIds = $rs['category_ids'];
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
        $contains = $this->getTagAwareQueryResultCacheCategory()->contains(self::MAIN_CATEGORY_IDS_DATA);

        if ($contains) {
            $result = $this->getTagAwareQueryResultCacheCategory()->fetch(self::MAIN_CATEGORY_IDS_DATA);
        } else {
            $connection = $this->getEntityManager()->getConnection();

            $query  = '
                SELECT c.id, c.category_name, conf.key_words, conf.negative_key_words FROM category AS c
                INNER JOIN category_configurations AS conf ON conf.category_id_id = c.id
                WHERE 
                EXISTS(SELECT 1 FROM category_relations WHERE main_category_id = c.id)
                AND
                NOT EXISTS(SELECT 1 FROM category_relations WHERE sub_category_id = c.id)
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

            $mainCategoryWords = [];
            foreach ($mainCategoryIds as $main) {
                if (isset($main['category_name'])) {
                    $mainCategoryWords['category_ids'][] = $main['id'];
                    $mainCategoryWords['categories'][$main['category_name']]['positive'] = $main['key_words'];
                    $mainCategoryWords['categories'][$main['category_name']]['id'] = $main['id'];
                    
                    $negative_key_words = null;
                    if (strlen($main['negative_key_words'])) {
                        $negative_key_words = implode(', ', array_map(function ($v) {return '!' . $v;}, explode(',', $main['negative_key_words'])));
                    }

                    $mainCategoryWords['categories'][$main['category_name']]['negative'] = $negative_key_words;
                    if ($negative_key_words) {
                        $mainCategoryWords['common'][] = '(' . $main['key_words'] . ', ' . $negative_key_words . ')';
                    } else {
                        $mainCategoryWords['common'][] = $main['key_words'];
                    }

                }
            }
            $this->getTagAwareQueryResultCacheCategory()
                ->save(self::MAIN_CATEGORY_IDS_DATA, $mainCategoryWords,86399);
            $result = $mainCategoryWords;
        }


        return $result;
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
     * @param ParameterBag $parameterBag
     * @param int $depth
     * @param bool $explain
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function matchCategoryWithSub(
        ParameterBag $parameterBag, int $depth = 3, $explain = false
    )
    {
        $connection = $this->getEntityManager()->getConnection();
//        if ($product->getMatchMainCategoryData()) {
//            $mainSearch = $product->getMatchMainCategoryData();
//        }

//        if (!isset($mainSearch) && $product && !$product->getMatchMainCategoryData()) {
//            if (!$product->getCategory()) {
//                return [];
//            }
//            $checkMainCategoriesResult = $this->isMatchPlainCategoriesString(
//                $parameterBag->get(CategoryService::MAIN_CATEGORY_SEARCH),
//                $parameterBag->get(CategoryService::MAIN_SEARCH),
//                true
//            );
//
//            if (isset($checkMainCategoriesResult['match']) && !$checkMainCategoriesResult['match']) {
//                return [];
//            }
//            if (isset($checkMainCategoriesResult[CategoryService::MAIN_SEARCH])) {
//                $mainSearch =
//                    $this->getHelpers()
//                        ->handleSearchValue($checkMainCategoriesResult[CategoryService::MAIN_SEARCH], false);
//
//                $product->setMatchMainCategoryData($mainSearch);
//            }
//        }
        $params = [];
        $ids = $parameterBag->get( CategoryService::MAIN_SEARCH );
        foreach ($ids as $key=>$id) {
            $params[':main_id' . $key] = $id;
            $types[':main_id' . $key] = \PDO::PARAM_INT;
        }
        if (!count($params)) {
            return [];
        }
        $idsMain = implode(',', array_keys($params));
        if ($depth > 1) {
            $subMainSearch = $parameterBag->get(CategoryService::SUB_MAIN_SEARCH);
        }

        if ($depth > 2) {
            $subSubMainSearch = $parameterBag->get(CategoryService::SUB_MAIN_SEARCH);
        }

        $query = '
            SELECT             
            DISTINCT ca.id';

//        if ($explain === true) {
//            $query .= '
//                ,ca.category_name
//                ,ts_headline(\'my_swedish\',cc.key_words, to_tsquery(\'my_swedish\', :main_search_parial_category))
//                ,ts_rank_cd(cc.common_fts, to_tsquery(\'pg_catalog.swedish\', :main_search_parial_category)) AS  main_runk
//            ';
//        }

        if ($depth > 1) {
            $query .= '
                ,cr_main.sub_category_id AS sub_ctegory_id';
            if ($explain === true) {
                $query .= '
                    ,ts_headline(\'my_swedish\',crsub.key_words, to_tsquery(\'my_swedish\', :sub_main_search)) AS crsub_ts_headline
                    ,ts_headline(\'my_swedish\',crsub.negative_key_words, to_tsquery(\'my_swedish\', :sub_main_search)) AS crsub_ts_headline_negative_key_words
                    ,ts_rank_cd(crsub.common_fts, to_tsquery(\'my_swedish\', :sub_main_search)) AS  sub_runk
                ';
            }
        }

        if ($depth > 2) {
            $query .= '
                ,cr_main_main.sub_category_id AS sub_sub_category_id';
            if ($explain === true) {
                $query .= '
                    
                    ,ts_headline(\'my_swedish\',crsub_main.key_words, to_tsquery(\'my_swedish\', :sub_main_search))
                    ,ts_rank_cd(crsub_main.common_fts ,to_tsquery(\'my_swedish\', :sub_sub_main_search)) AS  sub_sub_runk
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
        if ($depth == 1) {
            $query .= '
                INNER JOIN category_relations as cr_main ON cr_main.main_category_id = ca.id
                INNER JOIN category_relations as cr_main_main ON cr_main_main.main_category_id = cr_main.sub_category_id
            ';
        }


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
                WHERE ca.id IN ('.$idsMain.')
            ';


        if ($depth > 1) {
            $query .= '
                AND crsub.common_fts @@ to_tsquery(\'my_swedish\', :sub_main_search)
                AND crsub.negative_key_words_fts @@ to_tsquery(\'my_swedish\', :sub_main_search) = FALSE
                AND (crsub.sizes = \'{}\' OR crsub.sizes = \'[]\')
            ';
        }

        if ($depth > 2) {
            $query .= '
                AND crsub_main.common_fts @@ to_tsquery(\'my_swedish\', :sub_sub_main_search)
                AND (crsub_main.sizes = \'{}\' OR crsub_main.sizes = \'[]\')
            ';
        }


        if ($depth > 1) {
//            $query .= '
//                    ,cr_main.sub_category_id';
            if ($explain === true) {
                $query .= '
                    ORDER BY sub_runk
                ';
            }
        }

        if ($depth > 2) {
//            $query .= '
//                    ,cr_main_main.sub_category_id';
            if ($explain === true) {
                $query .= '
                        ,sub_sub_runk
                ';
            }
        }

//        $params[':main_search_parial_category'] = $mainSearch;
//        $types[':main_search_parial_category'] = \PDO::PARAM_STR;

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
     * @param array $ids
     * @param string $matchData
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function matchSeparateCategoryById(
        array $ids, string $matchData
    )
    {
        $connection = $this->getEntityManager()->getConnection();
        $params = [];

        foreach ($ids as $key=>$id) {
            $params[':main_id' . $key] = $id;
            $types[':main_id' . $key] = \PDO::PARAM_INT;
        }
        $idsMain = implode(',', array_keys($params));

        $query = '
            SELECT             
            DISTINCT ca.id';

        $query .= '
                FROM category as ca
            ';

        $query .= '           
            INNER JOIN category_configurations as cc ON cc.category_id_id = ca.id
        ';

        $query .= '
                WHERE ca.id IN ('.$idsMain.')
                AND cc.common_fts @@ to_tsquery(\'my_swedish\', :main_search)
                AND cc.negative_key_words_fts @@ to_tsquery(\'my_swedish\', :main_search) = FALSE
            ';

        $params[':main_search'] = $matchData;
        $types[':main_search'] = \PDO::PARAM_STR;

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
     * @param string $productData
     * @param string $propertyName
     * @return bool|mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function matchKeyWordsByProperty(string $productData, string $propertyName)
    {
        $connection = $this->getEntityManager()->getConnection();

        $query = '
            select *
        	from admin_configuration as aconf
        	where aconf.property_name = :property_name
        	and aconf.data_fts @@ to_tsquery(\'my_swedish\', regexp_replace(:product_data, \'\', \'\'))
        ';

        $mainParams[':product_data'] = $productData;
        $mainType[':product_data'] = \PDO::PARAM_STR;

        $mainParams[':property_name'] = $propertyName;
        $mainType[':property_name'] = \PDO::PARAM_STR;

        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeQuery(
            $query,
            $mainParams,
            $mainType
        );
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return count($result);
    }

    /**
     * @param string $productCategoriesData
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function isMatchToMainCategory(
        string $productCategoriesData
    ) {
        $connection = $this->getEntityManager()->getConnection();

        $query  = '
                SELECT c.id, c.category_name, conf.key_words, conf.negative_key_words FROM category AS c
                INNER JOIN category_configurations AS conf ON conf.category_id_id = c.id
                WHERE 
                EXISTS(SELECT 1 FROM category_relations WHERE main_category_id = c.id)
                AND
                NOT EXISTS(SELECT 1 FROM category_relations WHERE sub_category_id = c.id)
                AND to_tsvector(\'my_swedish\', regexp_replace(:productCategoriesData, \'\', \'\')) 
                        @@ to_tsquery(\'my_swedish\', REGEXP_REPLACE(REGEXP_REPLACE(conf.key_words, \'\s+\', \'\', \'g\'), \',\', \':*|\', \'g\'))
                AND to_tsvector(\'my_swedish\', regexp_replace(:productCategoriesData, \'\', \'\')) 
                        @@ to_tsquery(\'my_swedish\', COALESCE (REGEXP_REPLACE(REGEXP_REPLACE(conf.negative_key_words, \'\s+\', \'\', \'g\'), \',\', \'|\', \'g\'), \'\')) = FALSE                        
            ';

        $mainParams[':productCategoriesData'] = $productCategoriesData;
        $mainType[':productCategoriesData'] = \PDO::PARAM_STR;
        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeQuery(
            $query,
            $mainParams,
            $mainType
        );

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $productCategoriesData
     * @param array $mainCategoriesData
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function isMatchPlainCategoriesString(
        string $productCategoriesData,
        array $mainCategoriesData,
        bool $explain = false
    )
    {
        $connection = $this->getEntityManager()->getConnection();

        $query = 'select
        	to_tsvector(\'pg_catalog.swedish\',:product_categories_data) 
        	@@ to_tsquery(\'pg_catalog.swedish\', :main_categories_data) as match';
        if ($explain) {
            $query .= '
                ,ts_headline(\'pg_catalog.swedish\', :product_categories_data, to_tsquery(\'pg_catalog.swedish\', :main_categories_data)) AS ts_headline_result
            ';
        }
        $mainCategoryWordsArray = [];
        foreach ($mainCategoriesData['categories'] as $keyCategoryData=>$categoryData) {
            $mainCategoryWordsArray[$keyCategoryData] = $categoryData['positive'];
        }
        $mainCategoryWordsString = $this->getHelpers()
            ->handleSearchValue(implode(',', $mainCategoryWordsArray), false);

        $mainParams[':product_categories_data'] = $productCategoriesData;
        $mainType[':product_categories_data'] = \PDO::PARAM_STR;

        $mainParams[':main_categories_data'] = $mainCategoryWordsString;
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
            $resultMainCategoryIds = [];
            if (preg_match_all("/<b>.*?<\/b>/iu", $result['ts_headline_result'], $m)) {
                $resultMainCategoryWords = [];

                $regTsHeadLightResult = array_shift($m);
                $explodeMainCategoriesData = explode('|', $mainCategoryWordsString);
                foreach ($explodeMainCategoriesData as $mainCategoryWord) {
                    foreach ($regTsHeadLightResult as $matchingWord) {

                        $mainCategoryWord = preg_replace('/:\*/', '', $mainCategoryWord);

                        if (mb_stripos($matchingWord, $mainCategoryWord) !== false) {
                            $resultMatchArray = preg_grep("/\b$mainCategoryWord\b/iu", $mainCategoryWordsArray);
                            foreach ($resultMatchArray as $nameMainCategory=>$matchPool) {
//                                if (isset($mainCategoriesData['categories'][$nameMainCategory]['negative'])) {
//                                    $resultMainCategoryWords[] = '(' . $mainCategoryWord . ', ' . $mainCategoriesData['categories'][$nameMainCategory]['negative'] . ')';
//                                } else {
//                                    $resultMainCategoryWords[] = $mainCategoryWord;
//                                }
                                
                                if (isset($mainCategoriesData['categories'][$nameMainCategory]['id'])) {
                                    $resultMainCategoryIds[] = $mainCategoriesData['categories'][$nameMainCategory]['id'];
                                }
                            }
                        }
                    }
                }
//                if (count($resultMainCategoryWords)) {
//                    $result[CategoryService::MAIN_SEARCH] = implode(', ', $resultMainCategoryWords);
//                }
            }

            return array_unique($resultMainCategoryIds);
        } else {
            return false;
        }
    }
}
