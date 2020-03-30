<?php

namespace App\Repository;

use App\Entity\Product;
use App\Services\Helpers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
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
     * @return false|int|mixed|mixed[]
     * @throws \Doctrine\DBAL\DBALException
     * @throws \InvalidArgumentException
     */
    public function fullTextSearch(ParamFetcher $paramFetcher, $count = false)
    {
        $connection = $this->getEntityManager()->getConnection();
        $limit = $paramFetcher->get('count');
        $offset = $limit * ($paramFetcher->get('page') - 1);
        $sortBy = $paramFetcher->get('sort_by');
        $sortOrder = $paramFetcher->get('sort_order');

        $sortBy = $this->getHelpers()->white_list($sortBy,
            ["id", "sku", "name",
                "description", "category", "price",
                "shipping", "currency", "instock", "productUrl", "imageUrl",
                "trackingUrl", "brand", "originalPrice", "ean", "manufacturerArticleNumber",
                "extras", "createdAt"], "Invalid field name " . $sortBy);
        $sortOrder = $this->getHelpers()->white_list($sortOrder, ["ASC", "DESC"], "Invalid ORDER BY direction " . $sortOrder);

        $searchField = $paramFetcher->get('search');
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
                        products_alias."originalPrice" AS "originalPrice",
                        products_alias.ean AS ean,
                        products_alias."manufacturerArticleNumber" AS "manufacturerArticleNumber",
                        products_alias.extras AS extras,
                        products_alias."createdAt" AS "createdAt",
                        products_alias.rank AS rank,
                        products_alias."brandRelationId" AS "brandRelationId",
                        array_agg(DISTINCT category_id) AS category_ids
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
                            brand_relation_id,
                            original_price AS "originalPrice",
                            ean,
                            manufacturer_article_number AS "manufacturerArticleNumber",
                            extras,
                            created_at AS "createdAt",
                            brand_relation_id AS "brandRelationId"
                        ';
            if (!$search) {
                $query .= '
                ,array_agg(DISTINCT category_id) AS category_ids';
            }
        }

        if ($search) {
            $query .= ',ts_rank_cd(to_tsvector(\'english\',name||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')), query) AS rank
                        FROM products , to_tsquery(:search) query
                        WHERE to_tsvector(\'english\',name||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')) @@ query
                        ORDER BY rank DESC) as products_alias';
        } else {
            $query .= '
                        FROM products AS products_alias
                        LEFT JOIN category_product cp on cp.product_id = products_alias.id
                    ';
        }

        if (is_array($paramFetcher->get('category_ids'))
            && array_search('0', $paramFetcher->get('category_ids'), true) === false) {
            $categoryIds = $paramFetcher->get('category_ids');
            $preparedInValuesCategory = array_combine(
                array_map(function ($key) {
                    return ':var_' . $key;
                }, array_keys($categoryIds)),
                array_values($categoryIds)
            );
            $bindKeysCategory = implode(',', array_keys($preparedInValuesCategory));
            if ($search) {
                $query .= '
                    LEFT JOIN category_product cp on cp.product_id = products_alias.id
                ';

            }
            $query .= "
                            WHERE cp.category_id IN ($bindKeysCategory)
                        ";
        }

        if (is_array($paramFetcher->get('brand_ids'))
            && array_search('0', $paramFetcher->get('brand_ids'), true) === false
        ) {
            $brandIds = $paramFetcher->get('brand_ids');
            $preparedInValuesBrand = array_combine(
                array_map(function ($key) {
                    return ':var_' . $key;
                }, array_keys($brandIds)),
                array_values($brandIds)
            );
            $bindKeysBrand = implode(',', array_keys($preparedInValuesBrand));
            $query .= "                           
                            WHERE products_alias.brand_relation_id IN ($bindKeysBrand)
                        ";
        }

        if (!$count) {
            $query .= '
                        GROUP BY id, sku, name, description, category, price, shipping, currency, instock, "productUrl", "imageUrl", "trackingUrl", brand, "originalPrice", ean, "manufacturerArticleNumber", extras, "createdAt", "brandRelationId"'
                . ($search ? ', rank ORDER BY rank DESC' : 'ORDER BY ' . '"' . $sortBy . '"' . ' ' . $sortOrder . '') . '
                        LIMIT :limit
                        OFFSET :offset;
                    ';
        }

        $statement = $connection->prepare($query);
        if (isset($preparedInValuesCategory)) {
            foreach ($preparedInValuesCategory as $key => $val) {
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
