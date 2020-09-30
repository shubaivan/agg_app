<?php

namespace App\Repository;

use App\Entity\CategorySection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method CategorySection|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorySection|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorySection[]    findAll()
 * @method CategorySection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorySectionRepository extends ServiceEntityRepository
{
    const SECTIONS_LIST = 'sections_list';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorySection::class);
    }

    /**
     * @return mixed
     */
    public function getSections()
    {
        $qb = $this->createQueryBuilder('s');

            $qb
                ->orderBy('s.id', Criteria::DESC);

        $query = $qb->getQuery();
        $query->enableResultCache(0, self::SECTIONS_LIST);

        $query
            ->useQueryCache(true);

        return $query->getResult();
    }
}
