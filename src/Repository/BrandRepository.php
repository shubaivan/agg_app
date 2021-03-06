<?php

namespace App\Repository;

use App\Cache\TagAwareQueryResultCacheBrand;
use App\Cache\TagAwareQueryResultCacheProduct;
use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Product;
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
 * @method Brand|null find($id, $lockMode = null, $lockVersion = null)
 * @method Brand|null findOneBy(array $criteria, array $orderBy = null)
 * @method Brand[]    findAll()
 * @method Brand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Brand[]|int getList(ResultCacheDriver $cache, QueryBuilder $qb, ParamFetcher $paramFetcher, bool $count = false, string $cacheId = '')
 * @method Brand[]|int getListParameterBag(ResultCacheDriver $cache, QueryBuilder $qb, ParameterBag $param, bool $count = false, string $cacheId = '')
 * @method ParameterBag handleDataTablesRequest(array $params)
 */
class BrandRepository extends ServiceEntityRepository
{
    const CACHE_HOT_BRAND_IDS = 'cache_hot_brand_ids';
    const BRAND_SEARCH_CONT = "brand_search_cont";
    const BRAND_SEARCH_COLLECTION = "brand_search_collection";
    const BRAND_FULL_TEXT_SEARCH = 'brand_full_text_search';

    use PaginationRepository;
    use DataTablesApproachRepository;

    /**
     * @var Helpers
     */
    private $helpers;

    /**
     * @var TagAwareQueryResultCacheBrand
     */
    private $tagAwareQueryResultCacheBrand;

    /**
     * @var TagAwareQueryResultCacheProduct
     */
    private $tagAwareQueryResultCacheProduct;

    /**
     * BrandRepository constructor.
     * @param ManagerRegistry $registry
     * @param Helpers $helpers
     * @param TagAwareQueryResultCacheBrand $tagAwareQueryResultCacheBrand
     * @param TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct
     */
    public function __construct(
        ManagerRegistry $registry,
        Helpers $helpers,
        TagAwareQueryResultCacheBrand $tagAwareQueryResultCacheBrand,
        TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct
    )
    {
        parent::__construct($registry, Brand::class);

        $this->helpers = $helpers;
        $this->tagAwareQueryResultCacheBrand = $tagAwareQueryResultCacheBrand;
        $this->tagAwareQueryResultCacheProduct = $tagAwareQueryResultCacheProduct;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return Brand[]|int
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
     * @param string $slug
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function matchExistBySlug(string $slug)
    {
        $dql = '
                SELECT b
                FROM App\Entity\Brand b
                WHERE ILIKE(b.slug, :search) = TRUE    
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
            ["id", "brandName", "createdAt"], "Invalid field name " . $sortBy);
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
                        SELECT COUNT(DISTINCT brand_alias.id)
                    ';
        } else {
            $templateId = Brand::getTemplateTitleId();
            $seoTitle = getenv($templateId);

            $seoDescrTemplId = Brand::getTemplateDescriptionId();
            $seoDescTempl = getenv($seoDescrTemplId);

            $query .= "
                    SELECT                         
                            DISTINCT brand_alias.id,
                            brand_alias.brand_name AS \"brandName\",
                            brand_alias.created_at AS \"createdAt\"
                            ,brand_alias.top AS top,
                            
                            CASE WHEN brand_alias.seo_title IS NULL OR brand_alias.seo_title='' THEN regexp_replace('$seoTitle', '{{ name }}', brand_alias.brand_name, 'g')
                                ELSE brand_alias.seo_title
                            END as seo_title,
                            
                            CASE WHEN brand_alias.seo_description IS NULL OR brand_alias.seo_description='' THEN regexp_replace('$seoDescTempl', '{{ name }}', brand_alias.brand_name, 'g')
                                ELSE brand_alias.seo_description
                            END as seo_description,
                            
                            brand_alias.slug
            ";

            if ($search) {
                $query .= '
                    ,ts_rank_cd(to_tsvector(\'pg_catalog.swedish\',brand_alias.brand_name), query_search) AS rank
            ';
            }
        }

        $query .= '
                FROM brand brand_alias
                INNER JOIN products AS pr ON pr.brand_relation_id = brand_alias.id   
        ';
        if ($search) {
            $query .= '
                JOIN to_tsquery(\'pg_catalog.swedish\', :search) query_search
                ON to_tsvector(\'pg_catalog.swedish\',brand_alias.brand_name) @@ query_search
            ';
        }

        if (!$count) {
            $query .= '
                    GROUP BY brand_alias.id';
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

        $this->getTagAwareQueryResultCacheBrand()->setQueryCacheTags(
            $query,
            $params,
            $types,
            [self::BRAND_FULL_TEXT_SEARCH],
            0, $count ? self::BRAND_SEARCH_CONT : self::BRAND_SEARCH_COLLECTION
        );
        [$query, $params, $types, $queryCacheProfile] = $this->getTagAwareQueryResultCacheBrand()
            ->prepareParamsForExecuteCacheQuery();

        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeCacheQuery(
            $query,
            $params,
            $types,
            $queryCacheProfile
        );

        if ($count) {
            $brands = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $brands = isset($brands[0]['count']) ? (int)$brands[0]['count'] : 0;
        } else {
            $brands = $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
        $statement->closeCursor();

        return $brands;
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
    public function facetFiltersBrand(
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
            ["id", "brandName", "createdAt"], "Invalid field name " . $sortBy);
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
                brand_alias.brand_name ~ :search_facet
            ';
        }

        if (!$count) {

            $query .= '
                GROUP BY brand_alias.id
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

        $this->getTagAwareQueryResultCacheBrand()->setQueryCacheTags(
            $query,
            $params,
            $types,
            ['brand_facet_filter'],
            0, $count ? "brand_facet_filter_cont" : "brand_facet_filter_collection"
        );
        [$query, $params, $types, $queryCacheProfile] = $this->getTagAwareQueryResultCacheBrand()
            ->prepareParamsForExecuteCacheQuery();

        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeCacheQuery(
            $query,
            $params,
            $types,
            $queryCacheProfile
        );

        if ($count) {
            $brands = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $brands = isset($brands[0]['count']) ? (int)$brands[0]['count'] : 0;
        } else {
            $brands = $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
        $statement->closeCursor();

        return $brands;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return Brand[]|int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBrandByIds(ParamFetcher $paramFetcher, $count = false)
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
     * @param array $params
     * @param bool $count
     * @param bool $total
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDataTablesData(
        array $params,
        bool $count = false,
        bool $total = false
    )
    {
        $parameterBag = $this->handleDataTablesRequest($params);
        if (isset($params['resource_shop_slug'])) {
            $parameterBag->set('resource_shop_slug', $params['resource_shop_slug']);
        }
        $limit = $parameterBag->get('limit');
        $offset = $parameterBag->get('offset');
        $sortBy = $parameterBag->get('sort_by');
        $sortOrder = $parameterBag->get('sort_order');
        $sortBy = $this->getHelpers()->white_list($sortBy,
            ["id", "brandName", "quantityProducts"], "Invalid field name " . $sortBy);

        if ($count) {
            $dql = '
                SELECT COUNT(DISTINCT b)
                FROM App\Entity\Brand b
                INNER JOIN b.products p
            ';
        } else {
            $dql = '
                SELECT 
                b.id, 
                b.brandName, 
                COUNT(DISTINCT p) as quantityProducts,
                b.top,
                b.seoTitle,
                b.seoDescription,
                b.slug,
                GROUP_CONCAT(DISTINCT sh.shopName SEPARATOR \'|\') as shop_names,
                GROUP_CONCAT(DISTINCT sh.slug SEPARATOR \'|\') as shop_slugs,
                \'edit\' as Action
                FROM App\Entity\Brand b
                INNER JOIN b.products p
                LEFT JOIN b.brandShopRelation sr
                LEFT JOIN sr.shop sh
            ';
        }

        if ($parameterBag->get('resource_shop_slug') && !$total) {
            $dql .= '
                INNER JOIN b.brandShopRelation bsr
            ';
        }
        $bindParams = [];
        $condition = ' WHERE ';
        $conditions = [];
        if ($parameterBag->get('search') && !$total) {
            $conditions[] = '
                            ILIKE(b.brandName, :var_search) = TRUE
                        ';
            $bindParams['var_search'] = '%'.$parameterBag->get('search').'%';

        }

        if ($parameterBag->get('resource_shop_slug') && !$total) {
            $conditions[] = '
                bsr.shopSlug = :resource_shop_slug
            ';
            $bindParams['resource_shop_slug'] = $parameterBag->get('resource_shop_slug');

        }

        if ($parameterBag->has('brandName')
            && $parameterBag->get('brandName') !== 'all'
            && !$total
        ) {
            $varBrandName = (bool)$parameterBag->get('brandName');

            $conditions[] = '
                b.top = :var_top_brand
            ';
            $bindParams['var_top_brand'] = $varBrandName;
        }

        if (count($conditions)) {
            $conditions = array_unique($conditions);
            $dql .= $condition . implode(' AND ', $conditions);
        }

        if (!$count) {
            $dql .= '
                GROUP BY b.id';
            if ($sortBy !== 'quantityProducts') {
                $sortBy = 'b.'.$sortBy;
            }
            $dql .= '
                ORDER BY ' . $sortBy . ' ' . $sortOrder;
        }

        $query = $this->getEntityManager()
            ->createQuery($dql);
        if (!$count) {
            $query
                ->setMaxResults($limit)
                ->setFirstResult($offset);
        }
        $query
            ->enableResultCache(0, self::CACHE_HOT_BRAND_IDS)
            ->useQueryCache(true);

        if ($bindParams) {
            $bindParams = array_unique($bindParams);
            $query
                ->setParameters($bindParams);
        }
        if ($count) {
            $result = $query->getSingleScalarResult();
        } else {
            $result = $query->getResult();
        }

        return $result;
    }

    /**
     * @param $object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($object)
    {
        $this->getPersist($object);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Brand $shop
     * @throws \Doctrine\ORM\ORMException
     */
    public function getPersist(Brand $shop)
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
     * @return TagAwareQueryResultCacheBrand
     */
    private function getTagAwareQueryResultCacheBrand(): TagAwareQueryResultCacheBrand
    {
        return $this->tagAwareQueryResultCacheBrand;
    }

    /**
     * @return TagAwareQueryResultCacheProduct
     */
    private function getTagAwareQueryResultCacheProduct(): TagAwareQueryResultCacheProduct
    {
        return $this->tagAwareQueryResultCacheProduct;
    }
}
