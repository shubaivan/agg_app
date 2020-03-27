<?php

namespace App\Repository;

use App\Entity\Product;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return false|int|mixed|mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fullTextSearch(ParamFetcher $paramFetcher, $count = false)
    {
        $connection = $this->getEntityManager()->getConnection();
        $limit = $paramFetcher->get('count');
        $offset = $limit * ($paramFetcher->get('page') - 1);
        $sortBy = $paramFetcher->get('sort_by');
        $sortOrder = $paramFetcher->get('sort_order');
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
        if ($count) {
            if ($search) {
                $query = '
                    SELECT 
                        COUNT(*)
                    FROM products, to_tsquery(:search) query
                    WHERE to_tsvector(\'english\',name||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')) @@ query;
                ';
                $statement = $connection->prepare($query);
                $statement->bindValue(':search', $search, \PDO::PARAM_STR);
            } else {
                $query = '
                    SELECT COUNT(*)
                    FROM products                    
                ';
                $statement = $connection->prepare($query);
            }
            $execute = $statement->execute();
            $products = $statement->fetchColumn();
            $products = (int)$products;
        } else {
            if ($search) {
                $query = '
                    SELECT 
                        id AS id,
                        sku As sku,
                        name AS "name",
                        description AS description,
                        category AS category,
                        price AS price,
                        shipping AS shipping,
                        currency AS currency,
                        instock AS instock,
                        product_url AS "productUrl",
                        image_url AS "imageUrl",
                        tracking_url AS "trackingUrl",
                        brand AS brand,
                        original_price AS "originalPrice",
                        ean AS ean,
                        manufacturer_article_number AS "manufacturerArticleNumber",
                        extras AS extras,
                        created_at AS "createdAt",
                        ts_rank_cd(to_tsvector(\'english\',name||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')), query) AS rank
                    FROM products, to_tsquery(:search) query
                    WHERE to_tsvector(\'english\',name||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')) @@ query
                    ORDER BY rank DESC
                    LIMIT :limit
                    OFFSET :offset;
                ';

                $statement = $connection->prepare($query);
                $statement->bindValue(':search', $search, \PDO::PARAM_STR);
                $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);
                $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
            } else {
                $query = '
                    SELECT 
                        id AS id,
                        sku As sku,
                        name AS "name",
                        description AS description,
                        category AS category,
                        price AS price,
                        shipping AS shipping,
                        currency AS currency,
                        instock AS instock,
                        product_url AS "productUrl",
                        image_url AS "imageUrl",
                        tracking_url AS "trackingUrl",
                        brand AS brand,
                        original_price AS "originalPrice",
                        ean AS ean,
                        manufacturer_article_number AS "manufacturerArticleNumber",
                        extras AS extras,
                        created_at AS "createdAt"
                    FROM products
                    ORDER BY ' . '"' . $sortBy . '"' . ' ' . $sortOrder . '
                    LIMIT :limit
                    OFFSET :offset;
                ';

                $statement = $connection->prepare($query);

//                $statement->bindValue(':sortBy', $sortBy, \PDO::PARAM_STR);
                $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
                $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);
//                $statement->bindValue(':sortOrder', $sortOrder, \PDO::PARAM_STR);
            }

            $execute = $statement->execute();
            $products = $statement->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $products;
    }
}
