<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\UserIp;
use App\Entity\UserIpProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * @method UserIpProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserIpProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserIpProduct[]    findAll()
 * @method UserIpProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserIpProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserIpProduct::class);
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param UserIp|null $userIp
     * @return mixed
     */
    public function getTopProductByIp(ParamFetcher $paramFetcher, ?UserIp $userIp = null)
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->select('
                products_alias.groupIdentity
                ')
            ->innerJoin('s.products', 'products_alias');
        if ($userIp) {
            $qb
                ->where('s.ips = :ip')
                ->setParameter('ip', $userIp);
        }

        $qb->groupBy('products_alias.groupIdentity')

            ->setFirstResult($paramFetcher->get('count') * ($paramFetcher->get('page') - 1))
            ->setMaxResults($paramFetcher->get('count'));

        $query = $qb->getQuery();
        $identities = $query->getResult();
        $result = [];
        foreach ($identities as $identity) {
            $result[] = $identity['groupIdentity'];
        }
        return $result;
    }

    /**
     * @param UserIp|null $userIp
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountTopProductByIp(?UserIp $userIp = null)
    {
        $qb = $this->createQueryBuilder('uip');
        $qb
            ->select('
            COUNT(DISTINCT IDENTITY(uip.products))
            ');

        if ($userIp) {
            $qb
                ->where('uip.ips = :ip')
                ->setParameter('ip', $userIp);
        }

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }
}
