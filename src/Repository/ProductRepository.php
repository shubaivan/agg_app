<?php

namespace App\Repository;

use App\Cache\TagAwareQueryResultCacheCommon;
use App\Cache\TagAwareQueryResultCacheProduct;
use App\Entity\Product;
use App\Services\Helpers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Cache\Cache as ResultCacheDriver;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Cache\ResultCacheStatement;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Product[]|int getList(ResultCacheDriver $cache, QueryBuilder $qb, ParamFetcher $paramFetcher, bool $count = false)
 */
class ProductRepository extends ServiceEntityRepository
{
    use PaginationRepository;

    const FACET_BRAND_QUERY_KEY = 'brand_query';
    const FACET_PRODUCT_QUERY_KEY = 'product_query';
    const FACET_EXTRA_FIELDS_QUERY_KEY = 'extra_fields_query';
    const FACET_CATEGORY_QUERY_KEY = 'category_query';
    const FACET_SHOP_QUERY_KEY = 'shop_query';
    const FACET_FILTERS = 'facet_filters_';

    private $mainQuery = '', $conditions = [], $variables = [], $params = [], $types = [], $queryMainCondition = '';

    /**
     * @var Helpers
     */
    private $helpers;

    /**
     * @var TagAwareQueryResultCacheCommon
     */
    private $tagAwareQueryResultCacheCommon;

    /**
     * @var TagAwareQueryResultCacheProduct
     */
    private $tagAwareQueryResultCacheProduct;

    /**
     * ProductRepository constructor.
     * @param Helpers $helpers
     * @param TagAwareQueryResultCacheCommon $tagAwareQueryResultCacheCommon
     */
    public function __construct(
        ManagerRegistry $registry,
        Helpers $helpers,
        TagAwareQueryResultCacheCommon $tagAwareQueryResultCacheCommon,
        TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct
    )
    {
        parent::__construct($registry, Product::class);

        $this->helpers = $helpers;
        $this->tagAwareQueryResultCacheCommon = $tagAwareQueryResultCacheCommon;
        $this->tagAwareQueryResultCacheProduct = $tagAwareQueryResultCacheProduct;
    }


    /**
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchAllExtrasFieldsWithCache()
    {
//        $this->getTagAwareQueryResultCacheCommon()
//            ->getTagAwareAdapter()->invalidateTags(['fetch_all_extras_fields']);

        $query = '
            select 
            DISTINCT e.key,  
            jsonb_agg(DISTINCT e.value) as fields 
            from products AS p 
            join jsonb_each_text(p.extras) e on true
            WHERE e.key != :exclude_key       
            GROUP BY e.key
        ';

        return $this->facetFiltersExtraFields(
            $query,
            [':exclude_key' => 'ALTERNATIVE_IMAGE'],
            [':exclude_key' => ParameterType::STRING]
        );
    }

    /**
     * @param string $query
     * @param array $params
     * @param array $type
     * @return array
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function facetFiltersExtraFields(
        string $query,
        array $params,
        array $type
    )
    {
        $connection = $this->getEntityManager()->getConnection();

        $this->getTagAwareQueryResultCacheCommon()->setQueryCacheTags(
            $query,
            $params,
            $type,
            ['fetch_all_extras_fields'],
            0, "extras_fields"
        );

        [$query, $params, $types, $queryCacheProfile] = $this->getTagAwareQueryResultCacheCommon()
            ->prepareParamsForExecuteCacheQuery();
        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeCacheQuery(
            $query, $params, $types, $queryCacheProfile
        );
        $fetchAll = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $statement->closeCursor();

        $result = [];
        foreach ($fetchAll as $key => $value) {
            if (isset($value['fields']) && isset($value['key'])) {
                preg_match_all('/\["([^_]+)"\]/', $value['fields'], $matches);
                if (isset($matches[1][0])) {
                    $value['fields'] = explode('", "', $matches[1][0]);
                }
                $result[$value['key']] = $value['fields'];
            }
        }

        return $result;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return Product[]|int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getProductByIds(ParamFetcher $paramFetcher, $count = false)
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
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return false|int|mixed|mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fullTextSearchByParameterFetcher(ParamFetcher $paramFetcher, $count = false)
    {
        $parameterBag = new ParameterBag($paramFetcher->all());

        return $this->fullTextSearchByParameterBag($parameterBag, $count);
    }

    /**
     * @param ParameterBag $parameterBag
     * @param bool $count
     * @return false|int|mixed|mixed[]
     * @throws \Doctrine\DBAL\DBALException
     * @throws \InvalidArgumentException
     */
    public function fullTextSearchByParameterBag(ParameterBag $parameterBag, $count = false)
    {
        $sort_by = isset($_REQUEST['sort_by']);
        $connection = $this->getEntityManager()->getConnection();

        $sortBy = $parameterBag->get('sort_by');
        $sortOrder = $parameterBag->get('sort_order');

        $sortBy = $this->getHelpers()->white_list($sortBy,
            ["id", "sku", "name",
                "description", "category", "price",
                "shipping", "currency", "instock", "productUrl", "imageUrl",
                "trackingUrl", "brand", "shop", "originalPrice", "ean",
                "manufacturerArticleNumber", "shopRelationId", "brandRelationId",
                "extras", "createdAt", "numberOfEntries"], "Invalid field name " . $sortBy);
        $sortOrder = $this->getHelpers()->white_list(
            $sortOrder,
            [Criteria::DESC, Criteria::ASC],
            "Invalid ORDER BY direction " . $sortOrder
        );

        if ($parameterBag->get('search')) {
            $parameterBag->set(
                'search',
                $this->getHelpers()
                    ->handleSearchValue($parameterBag->get('search'), false)
            );
        }
        $searchProductQuery = $this->getSearchProductQuery($parameterBag, $count, $sort_by, $sortBy, $sortOrder);
        if (!$count) {
            $this->mainQuery = $searchProductQuery;
        }

        $this->getTagAwareQueryResultCacheProduct()->setQueryCacheTags(
            $searchProductQuery,
            $this->params,
            $this->types,
            ['product_full_text_search'],
            0, $count ? "product_search_cont" : "product_search_collection"
        );
        [$query, $params, $types, $queryCacheProfile] = $this->getTagAwareQueryResultCacheProduct()
            ->prepareParamsForExecuteCacheQuery();

        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeCacheQuery(
            $query,
            $params,
            $types,
            $queryCacheProfile
        );

        if ($count) {
            $products = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $products = isset($products[0]['count']) ? (int)$products[0]['count'] : 0;
        } else {
            $products = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $this->prepareFacetFilterQueries();
        }
        $statement->closeCursor();

        return $products;
    }

    /**
     * @return Helpers
     */
    public function getHelpers(): Helpers
    {
        return $this->helpers;
    }

    /**
     * @return TagAwareQueryResultCacheCommon
     */
    public function getTagAwareQueryResultCacheCommon(): TagAwareQueryResultCacheCommon
    {
        return $this->tagAwareQueryResultCacheCommon;
    }

    /**
     * @return TagAwareQueryResultCacheProduct
     */
    public function getTagAwareQueryResultCacheProduct(): TagAwareQueryResultCacheProduct
    {
        return $this->tagAwareQueryResultCacheProduct;
    }

    /**
     * @return string
     */
    public function getEncryptMainQuery(): string
    {
        $encrypt_decrypt = $this->getHelpers()->encrypt_decrypt('encrypt', $this->mainQuery);

        return self::FACET_FILTERS . sha1($this->mainQuery);
    }

    /**
     * @param ParameterBag $parameterBag
     * @param string $query
     * @return string
     */
    private function prepareMainCondition(
        ParameterBag &$parameterBag,
        string &$query
    ): string
    {
        $this->queryMainCondition = '';
        if ($parameterBag->get('search')) {
            $this->queryMainCondition .= '
                JOIN to_tsquery(\'pg_catalog.swedish\', :search) query_search
                ON to_tsvector(\'pg_catalog.swedish\',coalesce(name,\'\')||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')||\' \'||coalesce(shop,\'\')) @@ query_search
        ';
        }

        $this->queryMainCondition .= '
                LEFT JOIN product_category cp on cp.product_id = products_alias.id
                LEFT JOIN product_category cpt on cpt.product_id = products_alias.id
                LEFT JOIN user_ip_product uip on uip.products_id = products_alias.id               
        ';

        if ($parameterBag->get('category_word')) {
            $parameterBag->set('category_word', $this->getHelpers()
                ->handleSearchValue($parameterBag->get('category_word'), false));
            $this->queryMainCondition .= '
                INNER JOIN product_category cps on cps.product_id = products_alias.id                                   
                INNER JOIN category cat on cps.category_id = cat.id         
            ';

            $this->queryMainCondition .= '
                JOIN to_tsquery(\'pg_catalog.swedish\', :category_word) cps_query_search
                ON to_tsvector(\'pg_catalog.swedish\',coalesce(category_name,\'\')||\' \') @@ cps_query_search
            ';
        }

        if (is_array($parameterBag->get('extra_array'))
            && array_search('0', $parameterBag->get('extra_array'), true) === false
        ) {
            $extraArray = $parameterBag->get('extra_array');
            $commonExtraConditionsArray = [];
            $preparedExtraArray = [];
            foreach ($extraArray as $key => $extraFieldData) {
                $commonExtraConditionArray = [];
                foreach ($extraFieldData as $childKey => $extraData) {
                    $preparedExtraArrayString = $this->getHelpers()
                        ->executeSerializerArray([$key => $extraData]);
                    $conditionExtraFields = 'products_alias.extras @> :var_extra_arrays_' . $key . '_' . $childKey;
                    $preparedExtraArray[':var_extra_arrays_' . $key . '_' . $childKey] = $preparedExtraArrayString;
                    array_push($commonExtraConditionArray, $conditionExtraFields);
                }
                $commonExtraConditionString = '(' . implode(' OR ', $commonExtraConditionArray) . ')';
                array_push($commonExtraConditionsArray, $commonExtraConditionString);
            }
            $commonExtraConditionsString = '(' . implode(' AND ', $commonExtraConditionsArray) . ')';

            array_push($this->conditions, $commonExtraConditionsString);
            $this->variables = array_merge($this->variables, $preparedExtraArray);
        }

        if (is_array($parameterBag->get('exclude_ids'))
            && array_search('0', $parameterBag->get('exclude_ids'), true) === false
        ) {
            $excludeIds = $parameterBag->get('exclude_ids');
            $preparedInValuesIds = array_combine(
                array_map(function ($key) {
                    return ':var_exclude_id' . $key;
                }, array_keys($excludeIds)),
                array_values($excludeIds)
            );
            $bindKeysIds = implode(',', array_keys($preparedInValuesIds));
            $conditionIds = "                           
                            products_alias.id NOT IN ($bindKeysIds)
                        ";
            array_push($this->conditions, $conditionIds);
            $this->variables = array_merge($this->variables, $preparedInValuesIds);
        }

        if (is_array($parameterBag->get('shop_ids'))
            && array_search('0', $parameterBag->get('shop_ids'), true) === false) {
            $shopIds = $parameterBag->get('shop_ids');
            $preparedInValuesShop = array_combine(
                array_map(function ($key) {
                    return ':var_shop_id' . $key;
                }, array_keys($shopIds)),
                array_values($shopIds)
            );
            $bindKeysShop = implode(',', array_keys($preparedInValuesShop));
            $conditionShop = "
                            products_alias.shop_relation_id IN ($bindKeysShop)
                        ";

            array_push($this->conditions, $conditionShop);
            $this->variables = array_merge($this->variables, $preparedInValuesShop);
        }

        if (is_array($parameterBag->get('category_ids'))
            && array_search('0', $parameterBag->get('category_ids'), true) === false) {
            $categoryIds = $parameterBag->get('category_ids');
            $preparedInValuesCategory = array_combine(
                array_map(function ($key) {
                    return ':var_category_id' . $key;
                }, array_keys($categoryIds)),
                array_values($categoryIds)
            );
            $bindKeysCategory = implode(',', array_keys($preparedInValuesCategory));
            $conditionCategory = "
                            cp.category_id IN ($bindKeysCategory)
                        ";
            array_push($this->conditions, $conditionCategory);
            $this->variables = array_merge($this->variables, $preparedInValuesCategory);
        }

        if (is_array($parameterBag->get('brand_ids'))
            && array_search('0', $parameterBag->get('brand_ids'), true) === false
        ) {
            $brandIds = $parameterBag->get('brand_ids');
            $preparedInValuesBrand = array_combine(
                array_map(function ($key) {
                    return ':var_brand_id' . $key;
                }, array_keys($brandIds)),
                array_values($brandIds)
            );
            $bindKeysBrand = implode(',', array_keys($preparedInValuesBrand));
            $conditionBrand = "                           
                            products_alias.brand_relation_id IN ($bindKeysBrand)
                        ";

            array_push($this->conditions, $conditionBrand);
            $this->variables = array_merge($this->variables, $preparedInValuesBrand);
        }

        if (count($this->conditions)) {
            $this->queryMainCondition .= 'WHERE ' . implode(' AND ', $this->conditions);
        }

        $query .= $this->queryMainCondition;

        return $this->queryMainCondition;
    }

    /**
     * @param ParameterBag $parameterBag
     * @param $count
     * @return array
     */
    private function prepareParamAndType(ParameterBag $parameterBag, bool $count): array
    {
        foreach ($this->variables as $key => $val) {
            $this->params[$key] = $val;
        }

        if ($parameterBag->get('search')) {
            $this->params[':search'] = $parameterBag->get('search');
            $this->types[':search'] = \PDO::PARAM_STR;
        }

        if ($parameterBag->get('category_word')) {
            $this->params[':category_word'] = $parameterBag->get('category_word');
            $this->types[':category_word'] = \PDO::PARAM_STR;
        }

        if (!$count) {
            $limit = (int)$parameterBag->get('count');
            $offset = $limit * ((int)$parameterBag->get('page') - 1);
            $this->params[':offset'] = $offset;
            $this->params[':limit'] = $limit;
            $this->types[':offset'] = \PDO::PARAM_INT;
            $this->types[':limit'] = \PDO::PARAM_INT;
        }

        return array($this->params, $this->types);
    }

    /**
     * @param ParameterBag $parameterBag
     * @param $count
     * @param bool $sort_by
     * @param $sortBy
     * @param $sortOrder
     * @return string
     */
    private function getSearchProductQuery(ParameterBag $parameterBag, $count, bool $sort_by, $sortBy, $sortOrder): string
    {
        $query = '';
        if ($count) {
            $query .= '
                        SELECT COUNT(DISTINCT products_alias.id)
                    ';
        } else {
            $query .= '
                    SELECT                         
                            products_alias.id,
                            products_alias.sku,
                            products_alias.name AS "name",
                            products_alias.description,
                            products_alias.category,
                            products_alias.price,
                            products_alias.shipping,
                            products_alias.currency,
                            products_alias.instock,
                            products_alias.product_url AS "productUrl",
                            products_alias.image_url AS "imageUrl",
                            products_alias.tracking_url AS "trackingUrl",
                            products_alias.brand,
                            products_alias.shop,
                            products_alias.original_price AS "originalPrice",
                            products_alias.ean,
                            products_alias.manufacturer_article_number AS "manufacturerArticleNumber",
                            products_alias.extras,
                            products_alias.created_at AS "createdAt",
                            products_alias.brand_relation_id AS "brandRelationId",
                            products_alias.shop_relation_id AS "shopRelationId",
                            array_agg(DISTINCT cpt.category_id) AS categoryIds,
                            COUNT(DISTINCT uip.id) as "numberOfEntries"
            ';

            if ($parameterBag->get('search')) {
                $query .= '
                    ,ts_rank_cd(to_tsvector(\'pg_catalog.swedish\',coalesce(name,\'\')||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')||\' \'||coalesce(shop,\'\')), query_search) AS rank
            ';
            }
        }

        $query .= '
                FROM products products_alias 
        ';

        $this->prepareMainCondition($parameterBag, $query);

        if (!$count) {
            $query .= '
                    GROUP BY products_alias.id';
            if ($parameterBag->get('search')) {
                $query .= ', query_search.query_search';
            }

            $query .=
                ($parameterBag->get('search') ?
                    ($sort_by
                        ? ' ORDER BY rank DESC, ' . '"' . $sortBy . '"' . ' ' . $sortOrder . ''
                        : ' ORDER BY rank DESC')
                    : ' ORDER BY ' . '"' . $sortBy . '"' . ' ' . $sortOrder . '') . '
                                          
                    LIMIT :limit
                    OFFSET :offset;
            ';
        }

        $this->prepareParamAndType($parameterBag, $count);

        return $query;
    }

    private function prepareFacetFilterQueries()
    {
        $cacheKey = $this->getEncryptMainQuery();

        $this->setFacetQueryFilterInProductCache(
            $cacheKey,
            self::FACET_CATEGORY_QUERY_KEY,
            $this->prepareCategoriesFacetFilterQuery()
        );

        $this->setFacetQueryFilterInProductCache(
            $cacheKey,
            self::FACET_EXTRA_FIELDS_QUERY_KEY,
            $this->prepareExtraFieldsFacetFilterQuery()
        );

        $this->setFacetQueryFilterInProductCache(
            $cacheKey,
            self::FACET_PRODUCT_QUERY_KEY,
            $this->mainQuery
        );

        $this->setFacetQueryFilterInProductCache(
            $cacheKey,
            self::FACET_BRAND_QUERY_KEY,
            $this->prepareBrandFacetFilterQuery()
        );
    }

    /**
     * @return string
     */
    private function prepareCategoriesFacetFilterQuery()
    {
        $connectionParams = $this->getEntityManager()->getConnection()->getParams();

        $queryFacet = '
            SELECT                         
                DISTINCT category_alias.id,
                category_alias.category_name AS "categoryName",
                category_alias.created_at AS "createdAt"
            FROM category category_alias
            INNER JOIN product_category product_category_alias ON product_category_alias.category_id = category_alias.id
            INNER JOIN products products_alias ON products_alias.id = product_category_alias.product_id                    
        ';

        $queryFacet .= str_replace(
            '\'pg_catalog.swedish\',coalesce(category_name,\'\')||\' \'',
            '\'pg_catalog.swedish\',coalesce(category_alias.category_name,\'\')||\' \'',
            $this->queryMainCondition
        );

        $queryFacet .= '
                    GROUP BY category_alias.id';

        $realCacheKey = 'query=' . $queryFacet .
            '&params=' . serialize($this->params) .
            '&types=' . serialize($this->types) .
            '&connectionParams=' . hash('sha256', serialize($connectionParams));

        return $realCacheKey;
    }

    /**
     * @return string
     */
    private function prepareExtraFieldsFacetFilterQuery()
    {
        $connectionParams = $this->getEntityManager()->getConnection()->getParams();

        $queryFacet = '
            SELECT 
            DISTINCT e.key,  
            jsonb_agg(DISTINCT e.value) as fields 
            FROM products AS products_alias 
            JOIN jsonb_each_text(products_alias.extras) e on true
        ';

        $queryFacet .= $this->queryMainCondition;

        $queryFacet .= (count($this->conditions) > 1 ? 'AND' : 'WHERE') . '
             e.key != :exclude_key 
            GROUP BY e.key
        ';

        $realCacheKey = 'query=' . $queryFacet .
            '&params=' . serialize(array_merge($this->params, [':exclude_key' => 'ALTERNATIVE_IMAGE'])) .
            '&types=' . serialize(array_merge($this->types, [':exclude_key' => ParameterType::STRING])) .
            '&connectionParams=' . hash('sha256', serialize($connectionParams));

        return $realCacheKey;
    }

    /**
     * @return string
     */
    private function prepareBrandFacetFilterQuery()
    {
        $connectionParams = $this->getEntityManager()->getConnection()->getParams();

        $queryFacet = '
            SELECT
                DISTINCT brand_alias.id,
                brand_alias.name AS "name",
                brand_alias.created_at AS "createdAt"
            FROM brand brand_alias
            INNER JOIN products products_alias ON products_alias.brand_relation_id = brand_alias.id
        ';

        $queryFacet .= $this->queryMainCondition;

        $queryFacet .= '
            GROUP BY brand_alias.id, brand_alias.name
        ';

        $realCacheKey = 'query=' . $queryFacet .
            '&params=' . serialize($this->params) .
            '&types=' . serialize($this->types) .
            '&connectionParams=' . hash('sha256', serialize($connectionParams));

        return $realCacheKey;
    }

    /**
     * @param string $cacheKey
     * @param string $realKey
     * @return bool
     */
    private function setFacetQueryFilterInProductCache(
        string $cacheKey,
        string $realKey,
        $queryExample
    )
    {
        if ($queryExample === null) {
            return true;
        }
        $queryData[] = $queryExample;
        $resultCache = $this->getTagAwareQueryResultCacheProduct();

        $data = $resultCache->fetch($cacheKey);
        if (!$data) {
            $data = [];
        }
        $data[$realKey] = $queryData;

        $resultCache->save($cacheKey, $data, 0);
        unset($queryExample);

        return true;
    }
}
