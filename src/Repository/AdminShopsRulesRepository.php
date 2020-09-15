<?php

namespace App\Repository;

use App\Cache\TagAwareQueryResultCacheCommon;
use App\Cache\TagAwareQueryResultCacheShop;
use App\Entity\AdminShopsRules;
use App\Services\Helpers;
use App\Services\Models\ProductService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Cache\ResultCacheStatement;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @method AdminShopsRules|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminShopsRules|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminShopsRules[]    findAll()
 * @method AdminShopsRules[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method ParameterBag handleDataTablesRequest(array $params)
 */
class AdminShopsRulesRepository extends ServiceEntityRepository
{
    use DataTablesApproachRepository;

    const DATA_TABLES = 'admin_shop_rule_data_tables';

    /**
     * @var Helpers
     */
    private $helpers;

    /**
     * @var TagAwareQueryResultCacheShop
     */
    private $tagAwareQueryResultCacheShop;

    /**
     * AdminShopsRulesRepository constructor.
     * @param ManagerRegistry $registry
     * @param Helpers $helpers
     * @param TagAwareQueryResultCacheShop $tagAwareQueryResultCacheShop
     */
    public function __construct(
        ManagerRegistry $registry,
        Helpers $helpers,
        TagAwareQueryResultCacheShop $tagAwareQueryResultCacheShop
    )
    {
        $this->helpers = $helpers;
        parent::__construct($registry, AdminShopsRules::class);
        $this->tagAwareQueryResultCacheShop = $tagAwareQueryResultCacheShop;
    }

    /**
     * @param string $value
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findConfByStore(string $value)
    {
        $contains = $this->getTagAwareQueryResultCacheShop()
            ->contains($value);
        if ($contains) {
            $result = $this->getTagAwareQueryResultCacheShop()->fetch($value);
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

            $this->getTagAwareQueryResultCacheShop()
                ->save($value, $result, 86399);
        }


        return $result;
    }

    /**
     * @param array $params
     * @param bool $count
     * @param bool $total
     * @return int|mixed[]
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function getDataTablesData(
        array $params,
        bool $count = false,
        bool $total = false
    )
    {
        $parameterBag = $this->handleDataTablesRequest($params);
        $connection = $this->getEntityManager()->getConnection();

        $sortBy = $parameterBag->get('sort_by');
        $sortOrder = $parameterBag->get('sort_order');
        $sortBy = $this->getHelpers()->white_list($sortBy,
            ["store", "quantityRules"], "Invalid field name " . $sortBy);

        if ($count) {
            $query = '
                SELECT COUNT(asr)
                FROM admin_shops_rules asr
            ';
        } else {
            $query = '
                SELECT 
                asr.id, 
                asr.store,                
                (SELECT COUNT(*) FROM jsonb_object_keys(asr.columns_keywords)) as "quantityRules",
                asr.columns_keywords as "columnsKeywords",
                 '.'\''.AdminShopsRules::availableActions().'\''.' as "Action"
                FROM admin_shops_rules asr
            ';
        }
        $bindParams = [];
        $bindTypes = [];
        $condition = ' WHERE ';
        $conditions = [];
        if ($parameterBag->get('search') && !$total) {
            $conditions[] = '
                            asr.store ILIKE :var_search
                        ';
            $bindParams['var_search'] = '%'.$parameterBag->get('search').'%';
            $bindTypes['var_search'] = \PDO::PARAM_STR;
        }

        if (count($conditions)) {
            $conditions = array_unique($conditions);
            $query .= $condition . implode(' AND ', $conditions);
        }

        if (!$count) {
            $query .= '
                GROUP BY asr.id';
            if ($sortBy !== 'quantityRules') {
                $sortBy = 'asr.'.$sortBy;
            } else {
                $sortBy = '"' . $sortBy . '"';
            }
            $query .= '
                ORDER BY ' . $sortBy . ' ' . $sortOrder;
        }


        if (!$count) {
            if ($parameterBag->get('limit')) {
                $query .= '                                          
                    LIMIT :limit
                ';
                $bindParams['limit'] = $parameterBag->get('limit');
                $bindTypes['var_search'] = \PDO::PARAM_INT;
            }

            if ($parameterBag->get('offset')) {
                $query .= '                                          
                    OFFSET :offset
                ';
                $bindParams['offset'] = $parameterBag->get('offset');
                $bindTypes['offset'] = \PDO::PARAM_INT;
            }
        }


        $this->getTagAwareQueryResultCacheShop()->setQueryCacheTags(
            $query,
            $bindParams,
            $bindTypes,
            [self::DATA_TABLES],
            0, $count ? self::DATA_TABLES . "_cont" : self::DATA_TABLES . "_collection"
        );
        [$query, $params, $types, $queryCacheProfile] = $this->getTagAwareQueryResultCacheShop()
            ->prepareParamsForExecuteCacheQuery();

        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeCacheQuery(
            $query,
            $params,
            $types,
            $queryCacheProfile
        );

        if ($count) {
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $result = isset($result[0]['count']) ? (int)$result[0]['count'] : 0;
        } else {
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
        $statement->closeCursor();

        return $result;
    }

    /**
     * @return mixed
     */
    public function getAvailableStoreNames()
    {
        $queryBuilder = $this->createQueryBuilder('s');

        return $queryBuilder
            ->select('s.store')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param AdminShopsRules $adminShopsRules
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(AdminShopsRules $adminShopsRules)
    {
        $this->getEntityManager()->persist($adminShopsRules);
        $this->getEntityManager()->flush();
    }

    /**
     * @return TagAwareQueryResultCacheShop
     */
    public function getTagAwareQueryResultCacheShop(): TagAwareQueryResultCacheShop
    {
        return $this->tagAwareQueryResultCacheShop;
    }

    /**
     * @return Helpers
     */
    public function getHelpers(): Helpers
    {
        return $this->helpers;
    }
}
