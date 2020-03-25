<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Serial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return Product[]|int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getProductsList(ParamFetcher $paramFetcher, $count = false)
    {
        $qb = $this->createQueryBuilder('s');

        if ($count) {
            $qb
                ->select('COUNT(s.id)');
            $query = $qb->getQuery();
            $result = $query->getSingleScalarResult();
        } else {
            $qb
                ->orderBy('s.' . $paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
                ->setFirstResult($paramFetcher->get('count') * ($paramFetcher->get('page') - 1))
                ->setMaxResults($paramFetcher->get('count'))
                ->orderBy('s.createdAt', Criteria::DESC);
            $query = $qb->getQuery();
            $result = $query->getResult();
        }

        return $result;
    }
}
