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
        $limit = $parameterBag->get('count');
        $offset = $limit * ($parameterBag->get('page') - 1);
        $sortBy = $parameterBag->get('sort_by');
        $sortOrder = $parameterBag->get('sort_order');

        $sortBy = $this->getHelpers()->white_list($sortBy,
            ["id", "sku", "name",
                "description", "category", "price",
                "shipping", "currency", "instock", "productUrl", "imageUrl",
                "trackingUrl", "brand", "shop", "originalPrice", "ean", "manufacturerArticleNumber",
                "extras", "createdAt"], "Invalid field name " . $sortBy);
        $sortOrder = $this->getHelpers()->white_list($sortOrder, [Criteria::ASC, Criteria::DESC], "Invalid ORDER BY direction " . $sortOrder);

        $searchField = $parameterBag->get('search');
        if ($searchField) {
            if (preg_match_all('/[ ]/', $searchField, $matches) > 0) {
                $search = str_replace(' ', ':*|', $searchField) . ':*';
            } else {
                $search = $searchField . ':*';
            }
        } else {
            $search = $searchField;
        }
        $query = '';
        if ($search) {
            $query = '
                    SELECT ';
            if ($count) {
                $query .= ' COUNT(DISTINCT id)';
            } else {
                $query .= '
                        products_alias.id AS id,
                        products_alias.sku As sku,
                        products_alias.name AS "name",
                        products_alias.description AS description,
                        products_alias.category AS category,
                        products_alias.price AS price,
                        products_alias.shipping AS shipping,
                        products_alias.currency AS currency,
                        products_alias.instock AS instock,
                        products_alias."productUrl" AS "productUrl",
                        products_alias."imageUrl" AS "imageUrl",
                        products_alias."trackingUrl" AS "trackingUrl",
                        products_alias.brand AS brand,
                        products_alias.shop AS shop,                    
                        products_alias."originalPrice" AS "originalPrice",
                        products_alias.ean AS ean,
                        products_alias."manufacturerArticleNumber" AS "manufacturerArticleNumber",
                        products_alias.extras AS extras,
                        products_alias."createdAt" AS "createdAt",
                        products_alias.rank AS rank,
                        products_alias."brandRelationId" AS "brandRelationId",
                        products_alias."shopRelationId" AS "shopRelationId",
                        array_agg(DISTINCT cpt.category_id) AS categoryIds
                        ';
            }

            $query .=
                '
                    FROM (';
        }
        if ($count && !$search) {
            $query .= '
                        SELECT COUNT(DISTINCT id)
                    ';
        } else {
            $query .= '
                    SELECT                         
                            id,
                            sku,
                            name AS "name",
                            description,
                            category,
                            price,
                            shipping,
                            currency,
                            instock,
                            product_url AS "productUrl",
                            image_url AS "imageUrl",
                            tracking_url AS "trackingUrl",
                            brand,
                            shop,
                            original_price AS "originalPrice",
                            ean,
                            manufacturer_article_number AS "manufacturerArticleNumber",
                            extras,
                            created_at AS "createdAt",
                            brand_relation_id AS "brandRelationId",
                            shop_relation_id AS "shopRelationId"
                        ';
            if (!$search) {
                $query .= '               
                ,array_agg(DISTINCT category_id) AS categoryIds';
            }
        }

        if ($search) {
            $query .= ',ts_rank_cd(to_tsvector(\'english\',name||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')||\' \'||coalesce(shop,\'\')), query) AS rank
                        FROM products , to_tsquery(:search) query
                        WHERE to_tsvector(\'english\',name||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')||\' \'||coalesce(shop,\'\')) @@ query
                        ORDER BY rank DESC) as products_alias';
            $query .= '
                LEFT JOIN product_category cp on cp.product_id = products_alias.id            ';
            $query .= '
                    LEFT JOIN product_category cpt on cpt.product_id = products_alias.id                ';
        } else {
            $query .= '
                        FROM products AS products_alias
                        LEFT JOIN product_category cp on cp.product_id = products_alias.id                    ';
        }

        $conditions = [];
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
            if ($search) {
                $conditionShop = "
                            products_alias.\"shopRelationId\" IN ($bindKeysShop)
                        ";
            } else {
                $conditionShop = "
                            products_alias.shop_relation_id IN ($bindKeysShop)
                        ";
            }

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
            if ($search) {
                $conditionBrand = "                           
                            products_alias.\"brandRelationId\" IN ($bindKeysBrand)
                        ";
            } else {
                $conditionBrand = "                           
                            products_alias.brand_relation_id IN ($bindKeysBrand)
                        ";
            }

            array_push($conditions, $conditionBrand);
        }
        if (count($conditions)) {
            $query .= 'WHERE ' . implode(' AND ', $conditions);
        }
        if (!$count) {
            $query .= '
                        GROUP BY id, sku, name, description, category, price, shipping, currency, instock, "productUrl", "imageUrl", "trackingUrl", brand, shop, "originalPrice", ean, "manufacturerArticleNumber", extras, "createdAt", "brandRelationId", "shopRelationId"'
                . ($search ? ($sort_by ? ', rank ORDER BY rank DESC, ' . '"' . $sortBy . '"' . ' ' . $sortOrder . '' : ', rank ORDER BY rank DESC') : 'ORDER BY ' . '"' . $sortBy . '"' . ' ' . $sortOrder . '') . '
                        LIMIT :limit
                        OFFSET :offset;
                    ';
        }

        $statement = $connection->prepare($query);
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
