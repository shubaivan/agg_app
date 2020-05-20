<?php

namespace App\Repository;

use App\Cache\TagAwareQueryResultCacheBrand;
use App\Cache\TagAwareQueryResultCacheCategory;
use App\Entity\Brand;
use App\Entity\Category;
use App\Services\Helpers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Cache\Cache as ResultCacheDriver;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Cache\ResultCacheStatement;
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
                ->handleSearchValue($searchField, $parameterBag->get('strict') === true);
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
                    ,ts_rank_cd(to_tsvector(\'pg_catalog.swedish\',coalesce(category_name,\'\')||\' \'), query_search) AS rank
                ';
            }
        }

        $query .= '
            FROM category category_alias 
        ';
        if ($search) {
            $query .= '
                JOIN to_tsquery(\'pg_catalog.swedish\', :search) query_search
                ON to_tsvector(\'pg_catalog.swedish\',coalesce(category_name,\'\')||\' \') @@ query_search
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
                ->handleSearchValue($searchField, $parameterBag->get('strict') === true);
        } else {
            $search = $searchField;
        }
        $aliasCategoryTable = '';

        if ($search) {
            if (!array_key_exists(':category_word', $params)) {
                $query .= '
                    JOIN to_tsquery(\'pg_catalog.swedish\', :search_facet) query_search_facet
                    ON to_tsvector(\'pg_catalog.swedish\',coalesce(' . $aliasCategoryTable . 'category_name,\'\')||\' \') @@ query_search_facet
                ';
            }
        }

        if (!$count) {
            $query .= '
                    GROUP BY category_alias.id';
            if ($search && !array_key_exists(':category_word', $params)) {
                $query .= ', query_search_facet.query_search_facet';
            }
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
                $params[':category_word'] .= '&' . $search;
            } else {
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
}
