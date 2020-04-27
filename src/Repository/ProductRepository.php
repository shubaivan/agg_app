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
        $connection = $this->getEntityManager()->getConnection();

        $query = '
            select 
            DISTINCT e.key,  
            jsonb_agg(DISTINCT e.value) as fields 
            from products AS p 
            join jsonb_each_text(p.extras) e on true
            WHERE e.key != :exclude_key       
            GROUP BY e.key
        ';

        $this->getTagAwareQueryResultCacheCommon()->setQueryCacheTags(
            $query,
            [':exclude_key' => 'ALTERNATIVE_IMAGE'],
            [':exclude_key' => ParameterType::STRING],
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
        $limit = (int)$parameterBag->get('count');
        $offset = $limit * ((int)$parameterBag->get('page') - 1);
        $sortBy = $parameterBag->get('sort_by');
        $sortOrder = $parameterBag->get('sort_order');

        $sortBy = $this->getHelpers()->white_list($sortBy,
            ["id", "sku", "name",
                "description", "category", "price",
                "shipping", "currency", "instock", "productUrl", "imageUrl",
                "trackingUrl", "brand", "shop", "originalPrice", "ean",
                "manufacturerArticleNumber", "shopRelationId", "brandRelationId",
                "extras", "createdAt"], "Invalid field name " . $sortBy);
        $sortOrder = $this->getHelpers()->white_list($sortOrder, [Criteria::DESC, Criteria::ASC], "Invalid ORDER BY direction " . $sortOrder);

        $searchField = $parameterBag->get('search');
        if ($searchField) {
            $search = $this->getHelpers()->handleSearchValue($searchField, false);
        } else {
            $search = $searchField;
        }
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
                            array_agg(DISTINCT cpt.category_id) AS categoryIds
            ';

            if ($search) {
                $query .= '
                    ,ts_rank_cd(to_tsvector(\'pg_catalog.swedish\',coalesce(name, ,\'\')||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')||\' \'||coalesce(shop,\'\')), query_search) AS rank
            ';
            }
        }

        $query .= '
                FROM products products_alias 
        ';
        if ($search) {
            $query .= '
                JOIN to_tsquery(\'pg_catalog.swedish\', :search) query_search
                ON to_tsvector(\'pg_catalog.swedish\',coalesce(name, ,\'\')||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')||\' \'||coalesce(shop,\'\')) @@ query_search
        ';
        }

        $query .= '
                LEFT JOIN product_category cp on cp.product_id = products_alias.id
                LEFT JOIN product_category cpt on cpt.product_id = products_alias.id               
        ';

        $categoryWord = $parameterBag->get('category_word');
        if ($categoryWord) {
            $categoryWord = $this->getHelpers()->handleSearchValue($categoryWord, false);
            $query .= '
                INNER JOIN product_category cps on cps.product_id = products_alias.id                                   
                INNER JOIN category cat on cps.category_id = cat.id         
            ';

            $query .= '
                JOIN to_tsquery(\'pg_catalog.swedish\', :category_word) cps_query_search
                ON to_tsvector(\'pg_catalog.swedish\',coalesce(category_name,\'\')||\' \') @@ cps_query_search
            ';
        }

        $conditions = [];
        $variables = [];
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

            array_push($conditions, $commonExtraConditionsString);
            $variables = array_merge($variables, $preparedExtraArray);
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
            array_push($conditions, $conditionIds);
            $variables = array_merge($variables, $preparedInValuesIds);
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

            array_push($conditions, $conditionShop);
            $variables = array_merge($variables, $preparedInValuesShop);
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
            array_push($conditions, $conditionCategory);
            $variables = array_merge($variables, $preparedInValuesCategory);
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

            array_push($conditions, $conditionBrand);
            $variables = array_merge($variables, $preparedInValuesBrand);
        }
        if (count($conditions)) {
            $query .= 'WHERE ' . implode(' AND ', $conditions);
        }
        if (!$count) {
            $query .= '
                    GROUP BY products_alias.id';
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
        foreach ($variables as $key => $val) {
            $params[$key] = $val;
        }

        if ($search) {
            $params[':search'] = $search;
            $types[':search'] = \PDO::PARAM_STR;
        }

        if ($categoryWord) {
            $params[':category_word'] = $categoryWord;
            $types[':category_word'] = \PDO::PARAM_STR;
        }

        if (!$count) {
            $params[':offset'] = $offset;
            $params[':limit'] = $limit;
            $types[':offset'] = \PDO::PARAM_INT;
            $types[':limit'] = \PDO::PARAM_INT;
        }

        $this->getTagAwareQueryResultCacheProduct()->setQueryCacheTags(
            $query,
            $params,
            $types,
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
}
