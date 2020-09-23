<?php

namespace App\Repository;

use App\Entity\ManuallyResourceJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ManuallyResourceJob|null find($id, $lockMode = null, $lockVersion = null)
 * @method ManuallyResourceJob|null findOneBy(array $criteria, array $orderBy = null)
 * @method ManuallyResourceJob[]    findAll()
 * @method ManuallyResourceJob[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ManuallyResourceJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ManuallyResourceJob::class);
    }

    // /**
    //  * @return ManuallyResourceJob[] Returns an array of ManuallyResourceJob objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ManuallyResourceJob
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param $object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($object)
    {
        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush();
    }
}
