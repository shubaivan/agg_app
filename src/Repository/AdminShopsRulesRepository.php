<?php

namespace App\Repository;

use App\Entity\AdminShopsRules;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AdminShopsRules|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminShopsRules|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminShopsRules[]    findAll()
 * @method AdminShopsRules[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminShopsRulesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminShopsRules::class);
    }

    /**
     * @param string $value
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findConfByStore(string $value)
    {
        $oneOrNullResult = $this->createQueryBuilder('a')
            ->select('
                a.id,
                a.columnsKeywords
            ')
            ->andWhere('a.store = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->enableResultCache()
            ->useQueryCache(true)
            ->getOneOrNullResult();
        $result = [];
        if (!is_null($oneOrNullResult)
            && isset($oneOrNullResult['columnsKeywords'])
            && count($oneOrNullResult['columnsKeywords'])
        ) {
            $result = $oneOrNullResult['columnsKeywords'];
        }

        return $result;
    }
}
