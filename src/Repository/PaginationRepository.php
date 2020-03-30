<?php


namespace App\Repository;

use App\Entity\Brand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Request\ParamFetcher;

trait PaginationRepository
{
    /**
     * @param QueryBuilder $qb
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return []|int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    final public function getList(
        QueryBuilder $qb,
        ParamFetcher $paramFetcher,
        $count = false)
    {
        if ($count) {
            $qb
                ->select('COUNT(s.id)');
            $query = $qb->getQuery();
            $result = $query->getSingleScalarResult();
        } else {
            $qb
                ->orderBy('s.' . $paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
                ->setFirstResult($paramFetcher->get('count') * ($paramFetcher->get('page') - 1))
                ->setMaxResults($paramFetcher->get('count'));
            $query = $qb->getQuery();
            $result = $query->getResult();
        }

        return $result;
    }
}