<?php

namespace App\Repository;

use App\Entity\CategorySection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CategorySection|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorySection|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorySection[]    findAll()
 * @method CategorySection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorySectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorySection::class);
    }

    // /**
    //  * @return CategorySection[] Returns an array of CategorySection objects
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
    public function findOneBySomeField($value): ?CategorySection
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
