<?php

namespace App\Repository;

use App\Entity\UserIp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserIp|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserIp|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserIp[]    findAll()
 * @method UserIp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserIpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserIp::class);
    }

    // /**
    //  * @return UserIp[] Returns an array of UserIp objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserIp
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
