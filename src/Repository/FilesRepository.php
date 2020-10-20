<?php

namespace App\Repository;

use App\Entity\Files;
use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Files|null find($id, $lockMode = null, $lockVersion = null)
 * @method Files|null findOneBy(array $criteria, array $orderBy = null)
 * @method Files[]    findAll()
 * @method Files[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Files::class);
    }

    /**
     * @param array $ids
     * @return Files[]|[]
     */
    public function getByIds(array $ids)
    {
        $queryBuilder = $this->createQueryBuilder('f');
        return $queryBuilder
            ->where($queryBuilder->expr()->in('f.id', $ids))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Shop $shop
     * @return mixed
     */
    public function getByShop(Shop $shop)
    {
        $queryBuilder = $this->createQueryBuilder('f');
        $result = $queryBuilder
            ->select('f.path')
            ->where('f.shop = :shop')
            ->setParameter('shop', $shop)
            ->getQuery()
            ->getResult();
        $prepareResult = [];
        foreach ($result as $file)
        {
            if (isset($file['path'])) {
                $prepareResult[] = $file['path'];
            }
        }
        return $prepareResult;
    }

    /**
     * @param $object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($object)
    {
        $this->getPersist($object);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove($object)
    {
        $this->getEntityManager()->remove($object);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $object
     * @throws \Doctrine\ORM\ORMException
     */
    public function getPersist($object): void
    {
        $this->getEntityManager()->persist($object);
    }
}
