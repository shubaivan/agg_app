<?php

namespace App\RepositoryMysql;

use App\EntityMysql\Colours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Colours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Colours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Colours[]    findAll()
 * @method Colours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ColoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Colours::class);
    }

    // /**
    //  * @return Colours[] Returns an array of Colours objects
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

    /**
     * @param $value
     * @return Colours|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByOriginalColorField($value): ?Colours
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.originalColor = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->enableResultCache()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }
    
}
