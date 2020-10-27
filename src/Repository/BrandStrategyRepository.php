<?php

namespace App\Repository;

use App\Entity\BrandStrategy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BrandStrategy|null find($id, $lockMode = null, $lockVersion = null)
 * @method BrandStrategy|null findOneBy(array $criteria, array $orderBy = null)
 * @method BrandStrategy[]    findAll()
 * @method BrandStrategy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BrandStrategyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BrandStrategy::class);
    }

    // /**
    //  * @return BrandStrategy[] Returns an array of BrandStrategy objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BrandStrategy
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
