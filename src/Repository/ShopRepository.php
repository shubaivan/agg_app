<?php

namespace App\Repository;

use App\Cache\TagAwareQueryResultCacheShop;
use App\Entity\AdminShopsRules;
use App\Entity\Category;
use App\Entity\Shop;
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
 * @method Shop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shop[]    findAll()
 * @method Shop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Shop[]|int getList(ResultCacheDriver $cache, QueryBuilder $qb, ParamFetcher $paramFetcher, bool $count = false, string $cacheId = '')
 * @method Shop[]|int getListParameterBag(ResultCacheDriver $cache, QueryBuilder $qb, ParameterBag $param, bool $count = false, string $cacheId = '')
 * @method ParameterBag handleDataTablesRequest(array $params)
 */
class ShopRepository extends ServiceEntityRepository
{
    use PaginationRepository;
    use DataTablesApproachRepository;

    const CACHE_ID_SHOP_LIST = 'cache_id_shop_list';
    const CACHE_ID_AVAILABLE_SHOP_FOR_RULE_LIST = 'cache_id_available_shop_for_rule_list';
    const SHOP_FULL_TEXT_SEARCH = 'shop_full_text_search';

    /**
     * @var Helpers
     */
    private $helpers;

    /**
     * @var TagAwareQueryResultCacheShop
     */
    private $tagAwareQueryResultCacheShop;

    /**
     * ShopRepository constructor.
     * @param ManagerRegistry $registry
     * @param Helpers $helpers
     * @param TagAwareQueryResultCacheShop $tagAwareQueryResultCacheShop
     */
    public function __construct(
        ManagerRegistry $registry,
        Helpers $helpers,
        TagAwareQueryResultCacheShop $tagAwareQueryResultCacheShop
    )
    {
        parent::__construct($registry, Shop::class);

        $this->helpers = $helpers;
        $this->tagAwareQueryResultCacheShop = $tagAwareQueryResultCacheShop;
    }

    /**
     * @param ParameterBag $parameterBag
     * @param array $properties
     * @return Shop[]|int
     */
    public function getAvailableShoForCreatingRule(
        ParameterBag $parameterBag,
        array $properties = []
    )
    {
        $availableStoreNames = $this->getEntityManager()->getRepository(AdminShopsRules::class)
            ->getAvailableStoreNames();
        $prepareExcludeShopNames = [];
        array_walk($availableStoreNames, function ($v) use (&$prepareExcludeShopNames) {
            if (isset($v['store'])) {
                $prepareExcludeShopNames[] = $v['store'];
            }
        });

        $qb = $this->createQueryBuilder('s');
        if ($properties) {
            $array_map = array_map(function ($v) {
                return 's.'.$v;
            }, $properties);
            $implode = implode(', ', $array_map);
            $qb->select($implode);
        }

        $qb->where($qb->expr()->notIn('s.shopName', $prepareExcludeShopNames));

        return $this->getListParameterBag(
            $this->getEntityManager()->getConfiguration()->getResultCacheImpl(),
            $qb,
            $parameterBag,
            false,
            self::CACHE_ID_AVAILABLE_SHOP_FOR_RULE_LIST
        );
    }

    /**
     * @param ParameterBag $parameterBag
     * @param bool $count
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getShopsForSelect2(
        ParameterBag $parameterBag,
        bool $count = false
    ) {
        if ($count) {
            $dql = '
                SELECT COUNT(c.id) as count    
            ';
        } else {
            $dql = '
                SELECT 
                c.id as db_id, 
                c.shopName as text,
                c.slug as id  
            ';
        }
        $dql .= '
            FROM App\Entity\Shop c
        ';

        if ($parameterBag->get('search')) {
            $dql .= '
                WHERE ILIKE(c.shopName, :search) = TRUE
            ';
        }
        $page = $parameterBag->get('page');
        $query = $this->getEntityManager();
        $createQuery = $query
            ->createQuery($dql);
        if (!$count) {
            $createQuery
                ->setFirstResult($page <= 1 ? 0 : 25 * $page - 1)
                ->setMaxResults(25);
        }

        $createQuery
            ->enableResultCache(0, 'select2_shops_models')
            ->useQueryCache(true);

        if ($parameterBag->get('search')) {
            $createQuery->setParameter(':search', '%' . $parameterBag->get('search') . '%');
        }

        if ($count) {
            $result = $createQuery->getSingleScalarResult();
        } else {
            $result = $createQuery->getResult();
        }

        return $result;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return Shop[]|int
     */
    public function getEntityList(
        ParamFetcher $paramFetcher,
        $count = false
    )
    {
        $qb = $this->createQueryBuilder('s');

        return $this->getList(
            $this->getEntityManager()->getConfiguration()->getResultCacheImpl(),
            $qb,
            $paramFetcher,
            $count,
            self::CACHE_ID_SHOP_LIST
        );
    }

    /**
     * @param string $slug
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function matchExistBySlug(string $slug)
    {
        $dql = '
                SELECT s
                FROM App\Entity\Shop s
                WHERE ILIKE(s.slug, :search) = TRUE    
        ';

        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->useQueryCache(true);

        $query->setParameter(':search', $slug);

        return $query->getOneOrNullResult();
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
            ["id", "shopName", "createdAt"], "Invalid field name " . $sortBy);
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
                        SELECT COUNT(DISTINCT shop_alias.id)
                    ';
        } else {
            $query .= '
                    SELECT                         
                            DISTINCT shop_alias.id,
                            shop_alias.shop_name AS "shopName",
                            shop_alias.created_at AS "createdAt",
                            shop_alias.slug,
                            array_agg(DISTINCT sc.category_id) FILTER (WHERE sc.category_id IS NOT NULL) as "categoryIds",
                            array_agg(DISTINCT f.path) FILTER (WHERE f.path IS NOT NULL) as "filePath"
            ';

            if ($search) {
                $query .= '
                    ,ts_rank_cd(to_tsvector(\'pg_catalog.swedish\',shop_alias.shop_name), query_search) AS rank
            ';
            }
        }

        $query .= '
                FROM shop shop_alias 
                LEFT JOIN shop_category AS sc ON sc.shop_id = shop_alias.id
                LEFT JOIN files AS f ON f.shop_id = shop_alias.id 
        ';
        if ($search) {
            $query .= '
                JOIN to_tsquery(\'pg_catalog.swedish\', :search) query_search
                ON to_tsvector(\'pg_catalog.swedish\',shop_alias.shop_name) @@ query_search
            ';
        }

        if (!$count) {
            $query .= '
                    GROUP BY shop_alias.id';
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

        $this->getTagAwareQueryResultCacheShop()->setQueryCacheTags(
            $query,
            $params,
            $types,
            [self::SHOP_FULL_TEXT_SEARCH],
            0, $count ? "shop_search_cont" : "shop_search_collection"
        );
        [$query, $params, $types, $queryCacheProfile] = $this->getTagAwareQueryResultCacheShop()
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
            ["id", "shopName", "createdAt"], "Invalid field name " . $sortBy);
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

        if ($search) {
            $query .= preg_match('/\b(WHERE)\b/', $query, $matches) > 0 ? ' AND ' : ' WHERE ';
            $query .= '
                shop_alias.shop_name ~ :search_facet
            ';
        }

        if (!$count) {

            $query .= '
                GROUP BY shop_alias.id
            ';

            $query .=
                ' ORDER BY ' . '"' . $sortBy . '"' . ' ' . $sortOrder . '' . '                                          
                    LIMIT :limit
                    OFFSET :offset;
            ';

            $params = array_merge($params, [':offset' => $offset, ':limit' => $limit]);
            $types = array_merge($types, [':offset' => \PDO::PARAM_INT, ':limit' => \PDO::PARAM_INT]);
        }

        if ($search) {
            $params[':search_facet'] = $search;
            $types[':search_facet'] = \PDO::PARAM_STR;
        }

        $this->getTagAwareQueryResultCacheShop()->setQueryCacheTags(
            $query,
            $params,
            $types,
            ['shop_facet_filter'],
            0, $count ? "shop_facet_filter_cont" : "shop_facet_filter_collection"
        );
        [$query, $params, $types, $queryCacheProfile] = $this->getTagAwareQueryResultCacheShop()
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
     * @param bool $count
     * @return Shop[]|int
     */
    public function getShopsByNames(ParameterBag $parameterBag, $count = false)
    {
        $names = $parameterBag->get('names');
        if (is_array($names)) {
            $qb = $this->createQueryBuilder('s');
            $qb
                ->where($qb->expr()->in('s.shopName', $names));

            return $this->getListParameterBag(
                $this->getEntityManager()->getConfiguration()->getResultCacheImpl(),
                $qb,
                $parameterBag,
                $count
            );
        }

        return [];
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return Shop[]|int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getShopsByIds(ParamFetcher $paramFetcher, $count = false)
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
     * @param Shop $shop
     * @throws \Doctrine\ORM\ORMException
     */
    public function getPersist(Shop $shop)
    {
        $this->getEntityManager()->persist($shop);
    }

    /**
     * @return Helpers
     */
    private function getHelpers(): Helpers
    {
        return $this->helpers;
    }

    /**
     * @return TagAwareQueryResultCacheShop
     */
    private function getTagAwareQueryResultCacheShop(): TagAwareQueryResultCacheShop
    {
        return $this->tagAwareQueryResultCacheShop;
    }
}
