<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\UserIp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * @method UserIp|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserIp|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserIp[]    findAll()
 * @method UserIp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserIp[]|int getList($qb, $paramFetcher, $count)
 */
class UserIpRepository extends ServiceEntityRepository
{
    use PaginationRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserIp::class);
    }
}
