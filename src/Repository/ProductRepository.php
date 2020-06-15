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
            ["createdAt", "numberOfEntries", "price"],
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
        $searchProductQuery = $this->getMainSearchProductQuery(
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
            $this->prepareFacetFilterQueries();
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
        $resultIdentity = 'query=' . $this->mainQuery .
            '&params=' . serialize($this->params) .
            '&types=' . serialize($this->types);

        return self::FACET_FILTERS . sha1($resultIdentity);
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
        bool $count = false
    ): string
    {
        $this->queryMainCondition = '';
        if (!$count) {
            $this->queryMainCondition .= '
                LEFT JOIN user_ip_product uip on uip.products_id = products_alias.id               
            ';
        }

        if ($parameterBag->get('search')) {
            $this->queryMainCondition .= '               
                WHERE to_tsvector(\'pg_catalog.swedish\',
                 products_alias.name||products_alias.price||products_alias.description||products_alias.brand) 
                    @@ to_tsquery(\'pg_catalog.swedish\', :search)                      
        ';
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

            $this->queryMainCondition .= '
                LEFT JOIN product_category cp on cp.product_id = products_alias.id               
            ';

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
            $this->params[':offset'] = $offset;
            $this->params[':limit'] = $limit;
            $this->types[':offset'] = \PDO::PARAM_INT;
            $this->types[':limit'] = \PDO::PARAM_INT;
        }

        return array($this->params, $this->types);
    }
//{"2020-05-28 16:02:32"}	{34613,34614}	{"K-Way Le Vrai 3.0 Claude Jacka Fuchsia 12 years","K-Way Le Vrai 3.0 Claude Jacka Fuchsia 14 years"}	"34613"=>"K-Way Le Vrai 3.0 Claude Jacka Fuchsia 12 years", "34614"=>"K-Way Le Vrai 3.0 Claude Jacka Fuchsia 14 years"	"34613"=>"{\"SIZE\": \"152 cm\", \"COLOUR\": \"Rosa\", \"DELIVERY_TIME\": \"1-3 vardagar\"}", "34614"=>"{\"SIZE\": \"164 cm\", \"COLOUR\": \"Rosa\", \"DELIVERY_TIME\": \"1-3 vardagar\"}"	{925.00,1049.00}	"34613"=>"925.00", "34614"=>"1049.00"	"34613"=>"https://www.babyshop.com/images/711562/external-large.jpg", "34614"=>"https://www.babyshop.com/images/711562/external-large.jpg"

    /**
     * @param ParameterBag $parameterBag
     * @param $count
     * @param bool $sort_by
     * @param $sortBy
     * @param $sortOrder
     * @return string
     */
    private function getMainSearchProductQuery(
        ParameterBag $parameterBag,
        $count,
        bool $sort_by,
        $sortBy,
        $sortOrder): string
    {
        $mainQuery = '';
        if ($count) {
            $mainQuery .= '
                SELECT COUNT(*) FROM (
                    SELECT COUNT(DISTINCT main_products_alias.id)
            ';
        } else {
            $mainQuery .= '
            SELECT                         
                (array_agg(DISTINCT main_products_alias.created_at))[1]::TIMESTAMP AS "createdAt",
                (array_agg(DISTINCT main_products_alias.number_of_entries))[1]::INTEGER AS "numberOfEntries",
                (array_agg(DISTINCT main_products_alias.price))[1]::INTEGER AS price
                
                ,(array_agg(DISTINCT main_products_alias.shop))[1]::TEXT AS shop
                ,(array_agg(DISTINCT main_products_alias.shop_relation_id))[1]::INTEGER AS "shopRelationId"
                ,jsonb_agg(DISTINCT main_products_alias.extras) FILTER (WHERE main_products_alias.extras IS NOT NULL) AS extras
                
                ,hstore(array_agg(main_products_alias.id::text), array_agg(main_products_alias.brand::text)) AS "storeBrand"
                ,hstore(array_agg(main_products_alias.id::text), array_agg(main_products_alias.currency::text)) AS "storeCurrency"
                ,hstore(array_agg(main_products_alias.id::text), array_agg(main_products_alias.price::text)) AS "storePrice"
                ,hstore(array_agg(main_products_alias.id::TEXT), array_agg(main_products_alias.image_url)) AS "storeImageUrl"
                ,hstore(array_agg(main_products_alias.id::TEXT), array_agg(main_products_alias.name)) AS "storeNames"
                ,hstore(array_agg(main_products_alias.id::text), array_agg(main_products_alias.extras::text)) AS "storeExtras"
            ';

            if ($parameterBag->get('search')) {
                $mainQuery .= ',CAST((array_agg(DISTINCT main_products_alias.rank))[1] AS double precision) AS rank';
            }
        }

        $mainQuery .= '
                FROM (
            ';

        $mainQuery .= $this->getSearchProductQuery(
            $parameterBag,
            $count
        );

        $mainQuery .= '
            ) AS main_products_alias
            GROUP BY main_products_alias.group_identity';


        if (!$count) {
            $mainQuery .=
                ($parameterBag->get('search') ?
                    ($sort_by
                        ? ' ORDER BY rank DESC, ' . '"' . $sortBy . '"' . ' ' . $sortOrder . ''
                        : ' ORDER BY rank DESC')
                    : ' ORDER BY ' . '"' . $sortBy . '"' . ' ' . $sortOrder . '') . '
                                          
                    LIMIT :limit
                    OFFSET :offset;
            ';
        } else {
            $mainQuery .= ') as count';
        }

        return $mainQuery;
    }

    /**
     * @param ParameterBag $parameterBag
     * @param bool $count
     * @return string
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

    /**
     * @param ParameterBag $parameterBag
     * @return mixed[]
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function getProductRelations(ParameterBag $parameterBag)
    {
        $connection = $this->getEntityManager()->getConnection();
        $query = '
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
                ts_rank_cd(to_tsvector(\'pg_catalog.swedish\', products_alias.name||products_alias.price||products_alias.description||products_alias.brand), to_tsquery(\'pg_catalog.swedish\', :search)) AS rank
            FROM products products_alias
        
            WHERE to_tsvector(\'pg_catalog.swedish\', products_alias.name||products_alias.price||products_alias.description||products_alias.brand) @@ to_tsquery(\'pg_catalog.swedish\', :search)
            AND products_alias.id != :exclude_id
            ORDER BY rank DESC
            LIMIT :limit
            OFFSET :offset
        ';

        $search = $parameterBag->get('search');
        $resultData = $this->getHelpers()
            ->handleSearchValue($search, true);

        $limit = (int)$parameterBag->get('count');
        $offset = $limit * ((int)$parameterBag->get('page') - 1);


        $this->getTagAwareQueryResultCacheProduct()->setQueryCacheTags(
            $query,
            [
                ':search' => $resultData,
                ':exclude_id' => $parameterBag->get('exclude_id'),
                ':limit' => $limit,
                ':offset' => $offset,
            ],
            [
                ':search' => \PDO::PARAM_STR,
                ':exclude_id' => \PDO::PARAM_INT,
                ':limit' => \PDO::PARAM_INT,
                ':offset' => \PDO::PARAM_INT,
            ],
            ['product_full_text_search_relations'],
            0,
            "product_full_text_search_relations_cont"
        );
        [$query, $params, $types, $queryCacheProfile] = $this->getTagAwareQueryResultCacheProduct()
            ->prepareParamsForExecuteCacheQuery();

        /** @search ResultCacheStatement $statement */
        $statement = $connection->executeCacheQuery(
            $query,
            $params,
            $types,
            $queryCacheProfile
        );

        $products = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $statement->closeCursor();

        return $products;
    }

    public function groupProducts()
    {
//        SELECT
//
//array_agg(DISTINCT main_products_alias.sku) AS allsku,
//array_agg(DISTINCT main_products_alias."createdAt") AS "createdAt",
//array_agg(DISTINCT main_products_alias.id) AS ids,
//array_agg(DISTINCT main_products_alias.name) AS names,
//array_agg(DISTINCT main_products_alias.description) AS description,
//array_agg(DISTINCT main_products_alias.extras) AS extras,
//array_agg(DISTINCT main_products_alias.price) AS price,
//array_agg(DISTINCT main_products_alias."numberOfEntries") AS "numberOfEntries",
//array_agg(DISTINCT main_products_alias.shop) AS shop
//
//FROM (
//    SELECT
//products_alias.id,
//products_alias.group_identity,
//products_alias.sku,
//products_alias.shop,
//products_alias.name AS "name",
//products_alias.description,
//products_alias.category,
//products_alias.price,
//products_alias.extras,
//products_alias.created_at AS "createdAt",
//products_alias.brand_relation_id AS "brandRelationId",
//products_alias.shop_relation_id AS "shopRelationId",
//array_agg(DISTINCT cpt.category_id) AS categoryIds,
//COUNT(DISTINCT uip.id) as "numberOfEntries"
//
//    --,ts_rank_cd(to_tsvector('pg_catalog.swedish',coalesce(name,'')||' '||coalesce(description,'')||' '||coalesce(sku,'')||' '||coalesce(price,0)||' '||coalesce(category,'')||' '||coalesce(brand,'')||' '||coalesce(shop,'')), query_search) AS rank
//
//FROM products products_alias
//
//    -- JOIN to_tsquery('pg_catalog.swedish', 'short:*') query_search
//    -- ON to_tsvector('pg_catalog.swedish',coalesce(name,'')||' '||coalesce(description,'')||' '||coalesce(sku,'')||' '||coalesce(price,0)||' '||coalesce(category,'')||' '||coalesce(brand,'')||' '||coalesce(shop,'')) @@ query_search
//
//LEFT JOIN product_category cp on cp.product_id = products_alias.id
//LEFT JOIN product_category cpt on cpt.product_id = products_alias.id
//LEFT JOIN user_ip_product uip on uip.products_id = products_alias.id
//
//GROUP BY products_alias.id
//
//) AS main_products_alias
//
//GROUP BY main_products_alias.group_identity
//
//ORDER BY "numberOfEntries" DESC
//


    }
}
