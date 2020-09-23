<?php


namespace App\Repository;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\ORM\Cache;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Request\ParamFetcher;
use Doctrine\Common\Cache\Cache as ResultCacheDriver;
use Symfony\Component\HttpFoundation\ParameterBag;

trait PaginationRepository
{
    /**
     * @param ResultCacheDriver $cache
     * @param QueryBuilder $qb
     * @param ParameterBag $param
     * @param bool $count
     * @param string $cacheId
     * @return array|int|mixed
     */
    final public function getListParameterBag(
        ResultCacheDriver $cache,
        QueryBuilder $qb,
        ParameterBag $param,
        $count = false,
        string $cacheId = ''
    )
    {
        if ($count) {
            $query = $qb
                ->select('COUNT(s.id) as count')
                ->getQuery()
                ->setHydrationCacheProfile(new QueryCacheProfile(0, ($cacheId ? $cacheId : null), $cache))
                ->useQueryCache(true);

            $result = $query->getArrayResult();

            $result = $result[0]['count'] ?? 0;
        } else {
            if ($param->get('sort_by') && $param->get('sort_order')) {
                $qb
                    ->orderBy('s.' . $param->get('sort_by'), $param->get('sort_order'));
            }
            if ($param->get('count') && $param->get('page')) {
                $qb
                    ->setFirstResult($param->get('count') * ($param->get('page') - 1))
                    ->setMaxResults($param->get('count'));
            }

            $query =$qb->getQuery();

            if ($cacheId) {
                $query
                    ->enableResultCache(0, $cacheId);
            } else {
                $query
                    ->enableResultCache();
            }
            $query
                ->useQueryCache(true);

            $result = $query->getResult();
        }

        return $result;    
    }
    
    /**
     * @param ResultCacheDriver $cache
     * @param QueryBuilder $qb
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @param string $cacheId
     * @return array|int|mixed
     */
    final public function getList(
        ResultCacheDriver $cache,
        QueryBuilder $qb,
        ParamFetcher $paramFetcher,
        $count = false,
        string $cacheId = ''
    )
    {
        $parameterBag = new ParameterBag($paramFetcher->all());
        return $this->getListParameterBag(
            $cache,
            $qb,
            $parameterBag,
            $count,
            $cacheId
        );
    }
}