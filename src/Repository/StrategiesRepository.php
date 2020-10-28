<?php

namespace App\Repository;

use App\Entity\Strategies;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @method Strategies|null find($id, $lockMode = null, $lockVersion = null)
 * @method Strategies|null findOneBy(array $criteria, array $orderBy = null)
 * @method Strategies[]    findAll()
 * @method Strategies[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StrategiesRepository extends ServiceEntityRepository
{
    const SELECT_2_STRATEGIES_MODELS = 'select2_strategies_models';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Strategies::class);
    }


    /**
     * @param ParameterBag $parameterBag
     * @param bool $count
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCategoriesForSelect2(
        ParameterBag $parameterBag, bool $count = false
    )
    {
        if ($count) {
            $dql = '
                SELECT COUNT(s.id) as count    
            ';
        } else {
            $dql = '
                SELECT 
                s.id as core_id, 
                s.strategyName as text,
                s.description,
                s.slug as id,
                s.slug slug  
            ';
        }
        $dql .= '
            FROM App\Entity\Strategies s
        ';

        if ($parameterBag->get('search')) {
            $dql .= '
                WHERE ILIKE(s.strategyName, :search) = TRUE
            ';
        }
        $page = $parameterBag->get('page');
        $query = $this->getEntityManager();
        $createQuery = $query
            ->createQuery($dql);
        if (!$count) {
            $createQuery
                ->setFirstResult($page <= 1 ? 0 : 25 * $page - 1)
                ->setMaxResults(25);
        }

        $createQuery
            ->enableResultCache(0, self::SELECT_2_STRATEGIES_MODELS)
            ->useQueryCache(true);

        if ($parameterBag->get('search')) {
            $createQuery->setParameter(':search', '%' . $parameterBag->get('search') . '%');
        }

        if ($count) {
            $result = $createQuery->getSingleScalarResult();
        } else {
            $result = $createQuery->getResult();
        }

        return $result;
    }
}
