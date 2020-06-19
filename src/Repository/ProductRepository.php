<?php

namespace App\Repository;

use App\Cache\TagAwareQueryResultCacheCommon;
use App\Cache\TagAwareQueryResultCacheProduct;
use App\Entity\Product;
use App\Services\Helpers;
use App\Services\Models\ProductService;
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
    const OFFSET = ':offset';
    const LIMIT = ':limit';
    const GROUPS_IDENTITY = 'groups_identity';
    const SHOP_IDS = 'shop_ids';
    const CATEGORY_IDS = 'category_ids';
    const NUMBER_OF_ENTRIES = "numberOfEntries";
    const CREATED_AT = "created_at";
    const PRICE = "price";
    const SORT_BY = 'sort_by';
    const SORT_ORDER = 'sort_order';

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
     * @var string
     */
    private $facetUniqIdentity;

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
                if ($value['key'] == Product::SIZE) {
                    $explodeSizes = explode(',', $value['fields']);
                    $resultValues = [];
                    foreach ($explodeSizes as $size) {
                        $size = preg_replace('/\[|]| |"/','',$size);
                        $size = substr($size, 1, -1);
                        $resultValues[] = $size;
                    }
                    $resultValues =array_unique($resultValues);
                } else {
                    preg_match_all('/\["([^_]+)"\]/', $value['fields'], $matches);
                    if (isset($matches[1][0])) {
                        $resultValues = explode('", "', $matches[1][0]);
                    }
                }
                $result[$value['key']] = $resultValues;
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
     * @return int|mixed[]
     * @throws \Doctrine\DBAL\Cache\CacheException
     * @throws \Exception
     */
    public function fullTextSearchByParameterBag(ParameterBag $parameterBag, $count = false)
    {
        $this->clearObjectPropertyData();

        $sort_by = isset($_REQUEST[self::SORT_BY]);
        $connection = $this->getEntityManager()->getConnection();

        $sortBy = $parameterBag->get(self::SORT_BY);
        $sortOrder = $parameterBag->get(self::SORT_ORDER);

        $sortBy = $this->getHelpers()->white_list($sortBy,
            [self::CREATED_AT, self::NUMBER_OF_ENTRIES, self::PRICE],
            "Invalid field name " . $sortBy
        );

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
        $searchProductQuery = $this->getRSearchProductQuery(
            $parameterBag,
            $count,
            $sort_by,
            $sortBy,
            $sortOrder
        );
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
            if (!$parameterBag->get(ProductService::GROUP_IDENTITY)
                && !$parameterBag->get(ProductService::EXCLUDE_GROUP_IDENTITY)
                && $parameterBag->get(ProductService::WITHOUT_FACET) == null
            ) {
                $this->prepareFacetFilterQueries();
            }
        }
        $statement->closeCursor();

        return $products;
    }


    /**
     * @return string
     */
    public function getEncryptMainQuery(): string
    {
//        $encrypt_decrypt = $this->getHelpers()->encrypt_decrypt('encrypt', $this->mainQuery);
        if ($this->facetUniqIdentity) {
            return $this->facetUniqIdentity;
        }
        $params = $this->params;

        if (isset($params[self::LIMIT])) {
            unset($params[self::LIMIT]);
        }
        if (isset($params[self::OFFSET])) {
            unset($params[self::OFFSET]);
        }

        $types = $this->types;

        if (isset($types[self::LIMIT])) {
            unset($types[self::LIMIT]);
        }
        if (isset($types[self::OFFSET])) {
            unset($types[self::OFFSET]);
        }
        $resultIdentity = 'query=' . $this->mainQuery .
            '&params=' . serialize($params) .
            '&types=' . serialize($types);
        $this->facetUniqIdentity = self::FACET_FILTERS . sha1($resultIdentity);
        return $this->facetUniqIdentity;
    }

    /**
     * @param ParameterBag $parameterBag
     * @param string $query
     * @param bool $count
     * @return string
     * @throws \Exception
     */
    private function prepareMainCondition(
        ParameterBag &$parameterBag,
        string &$query,
        bool $count = false,
        ?string $sortBy = null
    ): string
    {
        $this->queryMainCondition = '';
        if (!$count && $sortBy == self::NUMBER_OF_ENTRIES) {
            if ($parameterBag->get(ProductService::TOP_PRODUCTS)) {
                $this->queryMainCondition .= '
                    INNER JOIN user_ip_product uip on uip.products_id = products_alias.id               
                ';
            } else {
                $this->queryMainCondition .= '
                    LEFT JOIN user_ip_product uip on uip.products_id = products_alias.id               
                ';
            }
        } elseif ($count && $parameterBag->get(ProductService::TOP_PRODUCTS)) {
            $this->queryMainCondition .= '
                    INNER JOIN user_ip_product uip on uip.products_id = products_alias.id               
                ';
        }
        if (is_array($parameterBag->get(self::CATEGORY_IDS))
            && array_search('0', $parameterBag->get(self::CATEGORY_IDS), true) === false) {
            $this->queryMainCondition .= '
                LEFT JOIN product_category cp on cp.product_id = products_alias.id               
            ';
        }

        if ($parameterBag->get('search')) {
            $this->queryMainCondition .= '               
                WHERE products_alias.common_fts @@ to_tsquery(\'pg_catalog.swedish\', :search)                      
            ';
        }

        if ($parameterBag->get(ProductService::GROUP_IDENTITY)) {
            $conditionGroupIdentity = 'products_alias.group_identity = :group_identity';
            array_push($this->conditions, $conditionGroupIdentity);
            $this->variables = array_merge(
                $this->variables,
                [':group_identity' => $parameterBag->get(ProductService::GROUP_IDENTITY)]
            );
        }

        if ($parameterBag->get(ProductService::EXCLUDE_GROUP_IDENTITY)) {
            $conditionExcludeGroupIdentity = 'products_alias.group_identity != :exclude_group_identity';
            array_push($this->conditions, $conditionExcludeGroupIdentity);
            $this->variables = array_merge(
                $this->variables,
                [':exclude_group_identity' => $parameterBag->get(ProductService::EXCLUDE_GROUP_IDENTITY)]
            );
        }

        if ($parameterBag->get('category_word')) {
            throw new \Exception('deprecated key');
        }
//            $parameterBag->set('category_word', $this->getHelpers()
//                ->handleSearchValue($parameterBag->get('category_word'), false));
//            $this->queryMainCondition .= '
//                INNER JOIN product_category cps on cps.product_id = products_alias.id
//                INNER JOIN category cat on cps.category_id = cat.id
//            ';
//            $this->queryMainCondition .= preg_match(
//                '/\b(WHERE)\b/',
//                $this->queryMainCondition,
//                $matches
//            ) > 0 ? ' AND ' : ' WHERE ';
//            $this->queryMainCondition .= '
//                    cat.category_name ~ :category_word
//            ';
//        }

        if (is_array($parameterBag->get('extra_array'))
            && array_search('0', $parameterBag->get('extra_array'), true) === false
        ) {
            $extraArray = $parameterBag->get('extra_array');
            $commonExtraConditionsArray = [];
            $preparedExtraArray = [];
            foreach ($extraArray as $key => $extraFieldData) {
                $commonExtraConditionArray = [];
                foreach ($extraFieldData as $childKey => $extraData) {
                    $array = [$key => Product::SIZE == $key ? [$extraData] : $extraData];
                    $preparedExtraArrayString = $this->getHelpers()
                        ->executeSerializerArray($array);
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

        if (is_array($parameterBag->get(self::GROUPS_IDENTITY))
            && array_search('0', $parameterBag->get(self::GROUPS_IDENTITY), true) === false
        ) {
            $groupsIdentity = $parameterBag->get(self::GROUPS_IDENTITY);
            $preparedGroupsIdentity = array_combine(
                array_map(function ($key) {
                    return ':var_groups_identity' . $key;
                }, array_keys($groupsIdentity)),
                array_values($groupsIdentity)
            );
            $bindKeysGroups = implode(',', array_keys($preparedGroupsIdentity));
            $conditionIds = "                           
                            products_alias.group_identity IN ($bindKeysGroups)
                        ";
            array_push($this->conditions, $conditionIds);
            $this->variables = array_merge($this->variables, $preparedGroupsIdentity);
        }

        if (is_array($parameterBag->get(self::SHOP_IDS))
            && array_search('0', $parameterBag->get(self::SHOP_IDS), true) === false) {
            $shopIds = $parameterBag->get(self::SHOP_IDS);
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

        if (is_array($parameterBag->get(self::CATEGORY_IDS))
            && array_search('0', $parameterBag->get(self::CATEGORY_IDS), true) === false) {
            $categoryIds = $parameterBag->get(self::CATEGORY_IDS);
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

            $this->queryMainCondition .= preg_match(
                '/\b(WHERE)\b/',
                $this->queryMainCondition,
                $matches
            ) > 0 ? ' AND ' : ' WHERE ';

            $this->queryMainCondition .= implode(' AND ', $this->conditions);
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

//        if ($parameterBag->get('category_word')) {
//            $this->params[':category_word'] = $parameterBag->get('category_word');
//            $this->types[':category_word'] = \PDO::PARAM_STR;
//        }

        if (!$count) {
            $limit = (int)$parameterBag->get('count');
            $offset = $limit * ((int)$parameterBag->get('page') - 1);
            if ($offset) {
                $this->params[self::OFFSET] = $offset;
                $this->types[self::OFFSET] = \PDO::PARAM_INT;
            }

            if ($limit) {
                $this->params[self::LIMIT] = $limit;
                $this->types[self::LIMIT] = \PDO::PARAM_INT;
            }
        }

        return array($this->params, $this->types);
    }

    /**
     * @param ParameterBag $parameterBag
     * @param bool $count
     * @param bool $sort_by
     * @param $sortBy
     * @param $sortOrder
     * @return string
     * @throws \Exception
     */
    private function getRSearchProductQuery(
        ParameterBag $parameterBag,
        bool $count,
        bool $sort_by,
        $sortBy,
        $sortOrder
    ): string
    {
        $query = '';

        if ($count) {
            $query .= '
                SELECT COUNT(*) FROM (
                    SELECT COUNT(DISTINCT products_alias.id)
            ';
        } else {
            $query .= '
            SELECT
                products_alias.group_identity AS "groupIdentity"                                         
                ,(array_agg(DISTINCT products_alias.shop))[1]::TEXT AS shop
                ,(array_agg(DISTINCT products_alias.shop_relation_id))[1]::INTEGER AS "shopRelationId"
                ,jsonb_agg(DISTINCT products_alias.extras) FILTER (WHERE products_alias.extras IS NOT NULL) AS extras
                
                ,hstore(array_agg(products_alias.id::text), array_agg(products_alias.brand::text)) AS "storeBrand"
                
                ,hstore(array_agg(products_alias.id::text), array_agg(products_alias.price::text)) AS "storePrice"
                ,hstore(array_agg(products_alias.id::TEXT), array_agg(products_alias.image_url)) AS "storeImageUrl"
                ,hstore(array_agg(products_alias.id::TEXT), array_agg(products_alias.name)) AS "storeNames"
                ,hstore(array_agg(products_alias.id::text), array_agg(products_alias.extras::text)) AS "storeExtras"
            ';

            if ($parameterBag->get(ProductService::SELF_PRODUCT)) {
                $query .= '
                    ,hstore(array_agg(products_alias.id::text), array_agg(products_alias.product_url::text)) AS "storeProductUrl"
                    ,hstore(array_agg(products_alias.id::text), array_agg(products_alias.description::text)) AS "storeDescription"
                    ,hstore(array_agg(products_alias.id::text), array_agg(products_alias.instock::text)) AS "storeInstock"                                                            
                ';
            }

            if ($sortBy == self::NUMBER_OF_ENTRIES) {
                $query .= '
                    ,COUNT(DISTINCT uip.id) as "numberOfEntries"
                ';
            }

            if ($parameterBag->get('search')) {
                $query .= '                                      
                    ,SUM(ts_rank(products_alias.common_fts,to_tsquery(\'pg_catalog.swedish\', :search))) AS rank
                ';
            }
        }

        $query .= '
                FROM products products_alias 
        ';

        $this->prepareMainCondition($parameterBag, $query, $count, $sortBy);

        $query .= '
                    GROUP BY products_alias.group_identity';
        if ($sortBy == self::CREATED_AT) {
            $query .= '
                ,products_alias.created_at
            ';
        }

        if ($sortBy == self::PRICE) {
            $query .= '
                ,products_alias.price
            ';
        }

        $this->prepareParamAndType($parameterBag, $count);

        if (!$count) {
            if (!$parameterBag->get(ProductService::GROUP_IDENTITY)) {
                $query .=
                    ($parameterBag->get('search') ?
                        ($sort_by
                            ? ' ORDER BY rank DESC, ' . ($sortBy == self::NUMBER_OF_ENTRIES
                                ? ' ' . '"' . $sortBy . '"' . ' ' . $sortOrder . ''
                                : ' ' . 'products_alias.' . $sortBy . ' ' . $sortOrder)
                            : ' ORDER BY rank DESC')
                        : ($sortBy == self::NUMBER_OF_ENTRIES
                            ? ' ORDER BY ' . '"' . $sortBy . '"' . ' ' . $sortOrder . ''
                            : ' ORDER BY ' . 'products_alias.' . $sortBy . ' ' . $sortOrder)
                    );
            }

            if (isset($this->params[self::LIMIT])) {
                $query .= '                                          
                    LIMIT :limit
                ';
            }

            if (isset($this->params[self::OFFSET])) {
                $query .= '                                          
                    OFFSET :offset
                ';
            }
        } else {
            $query .= ') as count';
        }

        return $query;
    }

    /**
     * @param ParameterBag $parameterBag
     * @param bool $count
     * @return string
     * @throws \Exception
     */
    private function getSearchProductQuery(
        ParameterBag $parameterBag,
        bool $count = false
    ): string
    {
        $query = '';
        $query .= '
            SELECT                         
                products_alias.id,
                products_alias.name
                ,products_alias.image_url									
                ,products_alias.brand
                ,products_alias.shop
                ,products_alias.shop_relation_id
                                        
                ,products_alias.group_identity,
                products_alias.price,
                products_alias.currency,
                products_alias.extras,
                products_alias.created_at              
        ';
        if ($parameterBag->get(ProductService::SELF_PRODUCT)) {
            $query .= '
                ,products_alias.product_url
                ,products_alias.description
                ,products_alias.instock
            ';
        }

            if (!$count) {
            $query .= '
                ,COUNT(DISTINCT uip.id) as number_of_entries
            ';
            if ($parameterBag->get('search')) {
                $query .= '                                      
                    ,ts_rank_cd(
                      to_tsvector(
                        \'pg_catalog.swedish\',
                         products_alias.name||products_alias.price||products_alias.description||products_alias.brand
                         ),
                      to_tsquery(\'pg_catalog.swedish\',
                       :search)) AS rank
            ';
            }
        }

        $query .= '
                FROM products products_alias 
        ';

        $this->prepareMainCondition($parameterBag, $query, $count);

        $query .= '
                    GROUP BY products_alias.id';

        $this->prepareParamAndType($parameterBag, $count);

        return $query;
    }

    private function prepareFacetFilterQueries()
    {
        $cacheKey = $this->getEncryptMainQuery();

        $this->setFacetQueryFilterInProductCache(
            $cacheKey,
            self::FACET_SHOP_QUERY_KEY,
            $this->prepareShopFacetFilterQuery()
        );

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
    private function prepareShopFacetFilterQuery()
    {
        $queryFacet = '
            SELECT
                DISTINCT shop_alias.id,
                shop_alias.shop_name AS "shopName",
                shop_alias.created_at AS "createdAt"
            FROM shop shop_alias
            INNER JOIN products products_alias ON products_alias.shop_relation_id = shop_alias.id
        ';

        $queryFacet .= $this->clearFacetQueryFromJoin();

        $realCacheKey = $this->getRealCacheKeyFacet($queryFacet);

        return $realCacheKey;
    }


    /**
     * @return string
     */
    private function prepareCategoriesFacetFilterQuery()
    {
        $queryFacet = '
            SELECT                         
                DISTINCT category_alias.id,
                category_alias.category_name AS "categoryName",
                category_alias.created_at AS "createdAt"
            FROM category category_alias
            INNER JOIN product_category product_category_alias ON product_category_alias.category_id = category_alias.id
            INNER JOIN products products_alias ON products_alias.id = product_category_alias.product_id                    
        ';

        $queryFacet .= $this->clearFacetQueryFromJoin();

        $queryFacet = str_replace(
            'WHERE cat.category_name',
            'WHERE category_alias.category_name',
            $queryFacet
        );

        $queryFacet = str_replace(
            'INNER JOIN product_category cps on cps.product_id = products_alias.id                                   
                INNER JOIN category cat on cps.category_id = cat.id',
            '',
            $queryFacet
        );

        $realCacheKey = $this->getRealCacheKeyFacet($queryFacet);

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

        $queryFacet .= $this->clearFacetQueryFromJoin();
        $queryFacet .= preg_match('/\b(WHERE)\b/', $queryFacet, $matches) > 0 ? ' AND ' : ' WHERE ';
        $queryFacet .= '
             e.key != :exclude_key 
            GROUP BY e.key
        ';

        $realCacheKey = 'query=' . $queryFacet .
            '&&params=' . serialize(array_merge($this->params, [':exclude_key' => 'ALTERNATIVE_IMAGE'])) .
            '&&types=' . serialize(array_merge($this->types, [':exclude_key' => ParameterType::STRING])) .
            '&&connectionParams=' . hash('sha256', serialize($connectionParams));

        return $realCacheKey;
    }

    /**
     * @return string
     */
    private function prepareBrandFacetFilterQuery()
    {
        $queryFacet = '
            SELECT
                DISTINCT brand_alias.id,
                brand_alias.brand_name AS "brandName",
                brand_alias.created_at AS "createdAt"
            FROM brand brand_alias
            INNER JOIN products products_alias ON products_alias.brand_relation_id = brand_alias.id
        ';

        $queryFacet .= $this->clearFacetQueryFromJoin();

        $realCacheKey = $this->getRealCacheKeyFacet($queryFacet);

        return $realCacheKey;
    }

    private function clearFacetQueryFromJoin()
    {
        $result = str_replace(
            'LEFT JOIN user_ip_product uip on uip.products_id = products_alias.id',
            '',
            $this->queryMainCondition
        );

        return $result;
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

    /**
     * @return Helpers
     */
    private function getHelpers(): Helpers
    {
        return $this->helpers;
    }

    /**
     * @return TagAwareQueryResultCacheCommon
     */
    private function getTagAwareQueryResultCacheCommon(): TagAwareQueryResultCacheCommon
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
     * @param string $queryFacet
     * @param array $connectionParams
     * @return string
     */
    private function getRealCacheKeyFacet(string $queryFacet): string
    {
        $connectionParams = $this->getEntityManager()->getConnection()->getParams();

        $realCacheKey = 'query=' . $queryFacet .
            '&&params=' . serialize($this->params) .
            '&&types=' . serialize($this->types) .
            '&&connectionParams=' . hash('sha256', serialize($connectionParams));
        return $realCacheKey;
    }

    private function clearObjectPropertyData()
    {
        $this->mainQuery = '';
        $this->queryMainCondition = '';
        $this->conditions = [];
        $this->variables = [];
        $this->params = [];
        $this->types = [];
    }

    private function ff()
    {
        $t = '
        --EXPLAIN ANALYZE

SELECT                         
products_alias.group_identity

,(array_agg(DISTINCT products_alias.shop))[1]::TEXT AS shop
,(array_agg(DISTINCT products_alias.shop_relation_id))[1]::INTEGER AS "shopRelationId"
,jsonb_agg(DISTINCT products_alias.extras) FILTER (WHERE products_alias.extras IS NOT NULL) AS extras

,hstore(array_agg(products_alias.id::text), array_agg(products_alias.brand::text)) AS "storeBrand"
,hstore(array_agg(products_alias.id::text), array_agg(products_alias.currency::text)) AS "storeCurrency"
,hstore(array_agg(products_alias.id::text), array_agg(products_alias.price::text)) AS "storePrice"
,hstore(array_agg(products_alias.id::TEXT), array_agg(products_alias.image_url)) AS "storeImageUrl"
,hstore(array_agg(products_alias.id::TEXT), array_agg(products_alias.name)) AS "storeNames"
,hstore(array_agg(products_alias.id::text), array_agg(products_alias.extras::text)) AS "storeExtras"

,COUNT(DISTINCT uip.id) as "numberOfEntries"
,hstore(array_agg(products_alias.id::TEXT), array_agg(ts_rank_cd(to_tsvector(\'pg_catalog.swedish\',products_alias.name||products_alias.price||products_alias.description||products_alias.brand),
to_tsquery(\'pg_catalog.swedish\',
\'Yard:*|subSkjortor:*|Skjortor:*|Barn:*|ebbe:*|ÖVERDELAR:*|till:*|barn:*\'))::text)) AS rank                                 
--                     ,ts_rank_cd(
--                       to_tsvector(
--                         \'pg_catalog.swedish\',
--                          array_agg(products_alias.name)::TEXT||array_agg(products_alias.price)::TEXT||array_agg(products_alias.description)::TEXT||array_agg(products_alias.brand)::TEXT
--                         ),
--                       to_tsquery(\'pg_catalog.swedish\',
--                        \'Yard:*|subSkjortor:*|Skjortor:*|Barn:*|ebbe:*|ÖVERDELAR:*|till:*|barn:*\')) AS rank

FROM products products_alias 

LEFT JOIN user_ip_product uip on uip.products_id = products_alias.id
LEFT JOIN product_category cp on cp.product_id = products_alias.id                              

WHERE to_tsvector(\'pg_catalog.swedish\',
products_alias.name||products_alias.price||products_alias.description||products_alias.brand) 
@@ to_tsquery(\'pg_catalog.swedish\', \'Yard:*|subSkjortor:*|Skjortor:*|Barn:*|ebbe:*|ÖVERDELAR:*|till:*|barn:*\')                      

--AND 
--cp.category_id IN (2)

GROUP BY products_alias.group_identity 
ORDER BY 
--rank DESC,
"numberOfEntries" DESC  
LIMIT 20
        ';
    }
}
