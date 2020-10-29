<?php

namespace App\Repository;

use App\Entity\Brand;
use App\Entity\BrandShop;
use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BrandShop|null find($id, $lockMode = null, $lockVersion = null)
 * @method BrandShop|null findOneBy(array $criteria, array $orderBy = null)
 * @method BrandShop[]    findAll()
 * @method BrandShop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BrandShopRepository extends ServiceEntityRepository
{
    const CHECK_EXIST_BRAND_SHOP_RELATION = 'checkExistBrandShopRelation';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BrandShop::class);
    }

    /**
     * @param Brand $brand
     * @param Shop $shop
     * @return BrandShop|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkExistBrandShopRelation(Brand $brand, Shop $shop): ?BrandShop
    {
        $result = $this->createQueryBuilder('b')
            ->andWhere('b.brandSlug = :val_brand')
            ->andWhere('b.shopSlug = :val_shop')
            ->setParameters([
                'val_brand' => $brand->getSlug(),
                'val_shop' => $shop->getSlug()
            ])
            ->getQuery()
            ->enableResultCache(0, self::CHECK_EXIST_BRAND_SHOP_RELATION)
            ->useQueryCache(true)
            ->getOneOrNullResult()
        ;
        if (!$result) {
            $this->getEntityManager()
                ->getConfiguration()
                ->getResultCacheImpl()
                ->delete(self::CHECK_EXIST_BRAND_SHOP_RELATION);
        }

        return $result;
    }

    /**
     * @param $object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($object)
    {
        $this->getPersist($object);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $object
     * @throws \Doctrine\ORM\ORMException
     */
    public function getPersist($object): void
    {
        $this->getEntityManager()->persist($object);
    }
}
