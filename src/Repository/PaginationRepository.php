<?php


namespace App\Repository;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\ORM\Cache;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Request\ParamFetcher;
use Doctrine\Common\Cache\Cache as ResultCacheDriver;

trait PaginationRepository
{
    /**
     * @param ResultCacheDriver $cache
     * @param QueryBuilder $qb
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return array|int|mixed
     */
    final public function getList(
        ResultCacheDriver $cache,
        QueryBuilder $qb,
        ParamFetcher $paramFetcher,
        $count = false)
    {
        if ($count) {
            $query = $qb
                ->select('COUNT(s.id) as count')
                ->getQuery()
                ->setHydrationCacheProfile(new QueryCacheProfile(0, null, $cache))
                ->useQueryCache(true);

            $result = $query->getArrayResult();

            $result = $result[0]['count'] ?? 0;
        } else {
            $qb
                ->orderBy('s.' . $paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
                ->setFirstResult($paramFetcher->get('count') * ($paramFetcher->get('page') - 1))
                ->setMaxResults($paramFetcher->get('count'));

            $query =
                $qb->getQuery()
                    ->enableResultCache()
                    ->useQueryCache(true);
            $result = $query->getResult();
        }

        return $result;
    }
}