<?php

namespace App\Repository;

use App\Entity\CategoryConfigurations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CategoryConfigurations|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryConfigurations|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryConfigurations[]    findAll()
 * @method CategoryConfigurations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryConfigurationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryConfigurations::class);
    }

    // /**
    //  * @return CategoryConfigurations[] Returns an array of CategoryConfigurations objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategoryConfigurations
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
