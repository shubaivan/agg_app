<?php

namespace App\Repository;

use App\Entity\Product;
use App\Services\Helpers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Product[]|int getList($qb, $paramFetcher, $count)
 */
class ProductRepository extends ServiceEntityRepository
{
    use PaginationRepository;

    /**
     * @var Helpers
     */
    private $helpers;

    public function __construct(ManagerRegistry $registry, Helpers $helpers)
    {
        $this->helpers = $helpers;
        parent::__construct($registry, Product::class);
    }

    /**
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchAllExtrasFields()
    {
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
        $statement = $connection->prepare($query);
        $statement->bindValue(':exclude_key', 'ALTERNATIVE_IMAGE');
        $execute = $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_KEY_PAIR);
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

            return $this->getList($qb, $paramFetcher, $count);
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

        return $this->fullTextSearchByParameterBagOptimization($parameterBag, $count);
    }

    /**
     * @param ParameterBag $parameterBag
     * @param bool $count
     * @return false|int|mixed|mixed[]
     * @throws \Doctrine\DBAL\DBALException
     * @throws \InvalidArgumentException
     */
    public function fullTextSearchByParameterBagOptimization(ParameterBag $parameterBag, $count = false)
    {
        $sort_by = isset($_REQUEST['sort_by']);
        $connection = $this->getEntityManager()->getConnection();
        $limit = $parameterBag->get('count');
        $offset = $limit * ($parameterBag->get('page') - 1);
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
            if (preg_match_all('/[,]/', $searchField, $matches) > 0) {
                $result = preg_replace('!\s+!', ' ', $searchField);
                $result = preg_replace('/\s*,\s*/', ',', $result);
                $result = preg_replace('!\s!', '&', $result);
                $search = str_replace(',', ':*|', $result) . ':*';
            } else {
                $search = $searchField . ':*';
            }
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
                    ,ts_rank_cd(to_tsvector(\'english\',name||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')||\' \'||coalesce(shop,\'\')), query_search) AS rank
            ';
            }
        }

        $query .= '
                FROM products products_alias 
        ';
        if ($search) {
            $query .= '
                JOIN to_tsquery(:search) query_search
                ON to_tsvector(\'english\',name||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')||\' \'||coalesce(shop,\'\')) @@ query_search
        ';
        }

        $query .= '
                LEFT JOIN product_category cp on cp.product_id = products_alias.id
                LEFT JOIN product_category cpt on cpt.product_id = products_alias.id               
        ';

        $conditions = [];
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
        }
        if (count($conditions)) {
            $query .= 'WHERE ' . implode(' AND ', $conditions);
        }
        if (!$count) {
            $query .= '
                    GROUP BY id';
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

        $statement = $connection->prepare($query);

        if (isset($preparedExtraArray)) {
            foreach ($preparedExtraArray as $key => $val) {
                $statement->bindValue($key, $val);
            }
        }

        if (isset($preparedInValuesIds)) {
            foreach ($preparedInValuesIds as $key => $val) {
                $statement->bindValue($key, $val);
            }
        }

        if (isset($preparedInValuesCategory)) {
            foreach ($preparedInValuesCategory as $key => $val) {
                $statement->bindValue($key, $val);
            }
        }

        if (isset($preparedInValuesShop)) {
            foreach ($preparedInValuesShop as $key => $val) {
                $statement->bindValue($key, $val);
            }
        }

        if (isset($preparedInValuesBrand)) {
            foreach ($preparedInValuesBrand as $key => $val) {
                $statement->bindValue($key, $val);
            }
        }
        if ($search) {
            $statement->bindValue(':search', $search, \PDO::PARAM_STR);
        }

        if (!$count) {
            $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        }

        $execute = $statement->execute();

        if ($count) {
            $products = $statement->fetchColumn();
            $products = (int)$products;
        } else {
            $products = $statement->fetchAll(\PDO::FETCH_ASSOC);
        }


        return $products;
    }

    /**
     * @return Helpers
     */
    public function getHelpers(): Helpers
    {
        return $this->helpers;
    }
}
