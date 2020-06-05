<?php

namespace App\Repository;

use App\Entity\CategoryRelations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CategoryRelations|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryRelations|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryRelations[]    findAll()
 * @method CategoryRelations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRelationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryRelations::class);
    }

    // /**
    //  * @return CategoryRelations[] Returns an array of CategoryRelations objects
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
    public function findOneBySomeField($value): ?CategoryRelations
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
