<?php

namespace App\Repository;

use App\Cache\TagAwareQueryResultCacheCommon;
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
    /**
     * @var TagAwareQueryResultCacheCommon
     */
    private $tagAwareQueryResultCacheCommon;

    public function __construct(
        ManagerRegistry $registry,
        TagAwareQueryResultCacheCommon $tagAwareQueryResultCacheCommon
    )
    {
        parent::__construct($registry, AdminShopsRules::class);
        $this->tagAwareQueryResultCacheCommon = $tagAwareQueryResultCacheCommon;
    }

    /**
     * @param string $value
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findConfByStore(string $value)
    {
        $contains = $this->getTagAwareQueryResultCacheCommon()
            ->contains($value);
        if ($contains) {
            $result = $this->getTagAwareQueryResultCacheCommon()->fetch($value);
        } else {
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

            $this->getTagAwareQueryResultCacheCommon()
                ->save($value, $result, 86399);
        }


        return $result;
    }

    /**
     * @return TagAwareQueryResultCacheCommon
     */
    public function getTagAwareQueryResultCacheCommon(): TagAwareQueryResultCacheCommon
    {
        return $this->tagAwareQueryResultCacheCommon;
    }
}
