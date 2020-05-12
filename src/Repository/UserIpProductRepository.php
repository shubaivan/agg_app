<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\UserIp;
use App\Entity\UserIpProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * @method UserIpProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserIpProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserIpProduct[]    findAll()
 * @method UserIpProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserIpProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserIpProduct::class);
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param UserIp|null $userIp
     * @return mixed
     */
    public function getTopProductByIp(ParamFetcher $paramFetcher, ?UserIp $userIp = null)
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->select('
                IDENTITY(s.products) as product_id,               
                COUNT(DISTINCT s) as number_of_entries,
                products_alias.id,
                products_alias.sku,
                products_alias.name,
                products_alias.description,
                products_alias.category,
                products_alias.price,
                products_alias.shipping,
                products_alias.currency,
                products_alias.instock,
                products_alias.productUrl,
                products_alias.imageUrl,
                products_alias.trackingUrl,
                products_alias.brand,
                products_alias.shop,
                products_alias.originalPrice,
                products_alias.ean,
                products_alias.manufacturerArticleNumber,
                products_alias.extras,
                products_alias.createdAt,
                IDENTITY(products_alias.brandRelation) as brandRelationId,
                IDENTITY(products_alias.shopRelation) as shopRelationId,
                group_concat(categoryRelation.id) AS categoryIds
                ')
            ->innerJoin('s.products', 'products_alias')
            ->leftJoin('products_alias.categoryRelation', 'categoryRelation');

        if ($userIp) {
            $qb
                ->where('s.ips = :ip')
                ->setParameter('ip', $userIp);
        }

        $qb->groupBy('s.products')
            ->addGroupBy('products_alias')
            ->orderBy('number_of_entries', Criteria::DESC)
            ->setFirstResult($paramFetcher->get('count') * ($paramFetcher->get('page') - 1))
            ->setMaxResults($paramFetcher->get('count'));

        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * @param UserIp $userIp
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountTopProductByIp(UserIp $userIp)
    {
        $qb = $this->createQueryBuilder('uip');
        $qb
            ->select('
            COUNT(DISTINCT IDENTITY(uip.products))
            ')
//            ->where('uip.ips = :ip')
//            ->setParameter('ip', $userIp)
        ;
        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }
}
