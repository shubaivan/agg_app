<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Serial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
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
     * @return Product[]|int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getProductsList(ParamFetcher $paramFetcher, $count = false)
    {
        $qb = $this->createQueryBuilder('s');

        if ($count) {
            $qb
                ->select('COUNT(s.id)');
            $query = $qb->getQuery();
            $result = $query->getSingleScalarResult();
        } else {
            $qb
                ->orderBy('s.' . $paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
                ->setFirstResult($paramFetcher->get('count') * ($paramFetcher->get('page') - 1))
                ->setMaxResults($paramFetcher->get('count'))
                ->orderBy('s.createdAt', Criteria::DESC);
            $query = $qb->getQuery();
            $result = $query->getResult();
        }

        return $result;
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

        $searchField = $paramFetcher->get('search');
        if ($searchField) {
            $search = str_replace(' ', '|', $searchField);
        } else {
            $search = $searchField;
        }
        if ($count) {
            if ($search) {
                $query = '
                    SELECT 
                        COUNT(*)
                    FROM products, to_tsquery(:search) query
                    WHERE to_tsvector(\'english\',name||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,\'\')||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')) @@ query;
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
                        ts_rank_cd(to_tsvector(\'english\',name||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,\'\')||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')), query) AS rank
                    FROM products, to_tsquery(:search) query
                    WHERE to_tsvector(\'english\',name||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,\'\')||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')) @@ query
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
                    LIMIT :limit
                    OFFSET :offset;
                ';

                $statement = $connection->prepare($query);

                $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);
                $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
            }

            $execute = $statement->execute();
            $products = $statement->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $products;
    }
}
