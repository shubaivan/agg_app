<?php

namespace App\Repository;

use App\Cache\TagAwareQueryResultCacheCategoryConf;
use App\Entity\CategoryConfigurations;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Cache\ResultCacheStatement;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @method CategoryConfigurations|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryConfigurations|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryConfigurations[]    findAll()
 * @method CategoryConfigurations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryConfigurationsRepository extends ServiceEntityRepository
{
    const CATEGORY_CONF_SEARCH = 'category_conf_search';
    /**
     * @var TagAwareQueryResultCacheCategoryConf
     */
    private $tagAwareQueryResultCacheCategoryConf;

    /**
     * CategoryConfigurationsRepository constructor.
     * @param ManagerRegistry $registry
     * @param TagAwareQueryResultCacheCategoryConf $tagAwareQueryResultCacheCategoryConf
     */
    public function __construct(
        ManagerRegistry $registry,
        TagAwareQueryResultCacheCategoryConf $tagAwareQueryResultCacheCategoryConf
    )
    {
        $this->tagAwareQueryResultCacheCategoryConf = $tagAwareQueryResultCacheCategoryConf;
        parent::__construct($registry, CategoryConfigurations::class);
    }

    /**
     * @param array $params
     * @param bool $count
     * @param bool $total
     * @return int|mixed[]
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function getHoverMenuCategoryConf(
        array $params,
        bool $count = false,
        bool $total = false
    )
    {
        $parameterBag = new ParameterBag();

        $columnIndex = $params['order'][0]['column']; // Column index
        $columnName = $params['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $params['order'][0]['dir']; // asc or desc
        $columnSortOrder = ($columnSortOrder == 'desc' ? -1 : 1);

        if (isset($params['search']['value']) && strlen($params['search']['value'])) {
            $search = $params['search']['value'];
            $parameterBag->set('search', $search);
        }


        if (isset($params['draw'])) {
            $draw = $params['draw'];
            $parameterBag->set('page', $draw);
        }

        if (isset($params['start'])) {
            $offset = $params['start'];
            $parameterBag->set('offset', $offset);
        }

        if (isset($params['length'])) {
            $limit = $params['length'];
            $parameterBag->set('limit', $limit);
        }

        if (isset($params['columns']) && is_array($params['columns'])) {
            foreach ($params['columns'] as $column) {
                if (isset($column['search']['value'])
                    && isset($column['data'])
                    && strlen($column['search']['value'])
                ) {
                    $parameterBag->set($column['data'], $column['search']['value']);
                }
            }
        }
        
        return $this->getCategoryConfNative($parameterBag, $count, $total);
    }

    /**
     * @param ParameterBag $parameterBag
     * @param bool $count
     * @param bool $total
     * @return int|mixed[]
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function getCategoryConfNative(
        ParameterBag $parameterBag,
        bool $count = false,
        bool $total = false
    )
    {
        $sort_by = isset($_REQUEST['sort_by']);
        $connection = $this->getEntityManager()->getConnection();
        $limit = (int)$parameterBag->get('count');
        $offset = $limit * ((int)$parameterBag->get('page') - 1);

        $search = $parameterBag->get('search');

        $query = '';
        $params = [];
        $types = [];
        if ($count) {
            $query .= '
                        SELECT COUNT(DISTINCT category_alias.id)
                    ';
        } else {
            $query .= '
                    SELECT                         
                        category_alias.id,                       
                        category_alias.hot_category as "HotCategory",
                        category_alias.category_name as "CategoryName",
                        c_conf.key_words as "PositiveKeyWords",
                        c_conf.negative_key_words as "NegativeKeyWords",
                        \'action\' as "Action"
            ';
        }

        $query .= '
                FROM category_configurations AS c_conf
                INNER JOIN category AS category_alias ON category_alias.id = c_conf.category_id_id
                WHERE category_alias.custome_category = :custome_category 
        ';
        $params[':custome_category'] = true;
        $types[':custome_category'] = \PDO::PARAM_BOOL;
        if ($search && !$total) {
            $query .= '
                AND category_alias.category_name ILIKE :search
            ';
            $params[':search'] = '%' . $search . '%';
            $types[':search'] = \PDO::PARAM_STR;
        }

        if ($parameterBag->has('CategoryName')
            && $parameterBag->get('CategoryName') !== 'all'
        ) {
            $varCategoryName = (bool)$parameterBag->get('CategoryName');
            $query .= '
                AND category_alias.hot_category = :hot_category
            ';
            $params[':hot_category'] = $varCategoryName == true ? true : 0;
            $types[':search'] = \PDO::PARAM_BOOL;
        }

        if (!$count) {

            if ($parameterBag->get('limit')) {
                $query .= '
                    LIMIT :limit
                ';
                $params[':limit'] = $parameterBag->get('limit');
                $types[':limit'] = \PDO::PARAM_INT;
            }

            if ($parameterBag->get('offset') !== null) {
                $query .= '
                    OFFSET :offset
                ';
                $params[':offset'] = $parameterBag->get('offset');
                $types[':offset'] = \PDO::PARAM_INT;
            }
        }

        $this->getTagAwareQueryResultCacheCategoryConf()->setQueryCacheTags(
            $query,
            $params,
            $types,
            [self::CATEGORY_CONF_SEARCH],
            0, $count ? "category_conf_search_cont" : self::CATEGORY_CONF_SEARCH
        );
        [$query, $params, $types, $queryCacheProfile] = $this->getTagAwareQueryResultCacheCategoryConf()
            ->prepareParamsForExecuteCacheQuery();

        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeCacheQuery(
            $query,
            $params,
            $types,
            $queryCacheProfile
        );

        if ($count) {
            $brands = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $brands = isset($brands[0]['count']) ? (int)$brands[0]['count'] : 0;
        } else {
            $brands = $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
        $statement->closeCursor();

        return $brands;

    }

    /**
     * @param array $sizes
     * @param array $ids
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function matchSizeCategories(array $sizes, array $ids)
    {
        $params = [];
        $types = [];

        $sizesCond = [];
        foreach ($sizes as $key=>$size) {
            if (preg_match('/[0-9]+/', $size, $matchesSize)
            ) {
                if (count($matchesSize)) {
                    $exactlySize = array_shift($matchesSize);
                    $keyFoSize = ':size' . $key;
                    $params[$keyFoSize] = $exactlySize;
                    $types[$keyFoSize] = \PDO::PARAM_INT;
                    $qs = $keyFoSize . ' BETWEEN (cc.sizes ->>\'min\')::int AND (cc.sizes ->>\'max\')::int ';
                    $sizesCond[] = $qs;
                }
            }
        }

        if (!count($sizesCond)) {
            return [];
        }

        foreach ($ids as $key=>$id) {
            $params[':main_id' . $key] = $id;
            $types[':main_id' . $key] = \PDO::PARAM_INT;
        }
        if (!count($params)) {
            return [];
        }
        $idsMain = implode(',', array_keys($params));
        $connection = $this->getEntityManager()->getConnection();
        $query = '
            SELECT cc.category_id_id as id
    
            FROM category_configurations as cc
            INNER JOIN category_relations as cr ON cr.sub_category_id = cc.id
            WHERE 
                cr.main_category_id IN ('.$idsMain.')';

        $sizeCondStr = implode(' OR ', $sizesCond);

        $query .= '
            AND ('.$sizeCondStr.')
        ';

        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeQuery(
            $query,
            $params,
            $types
        );

        $idsCategorySize = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $idsCategorySize;
    }

    /**
     * @param $object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($object)
    {
        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush();
    }

    /**
     * @return TagAwareQueryResultCacheCategoryConf
     */
    public function getTagAwareQueryResultCacheCategoryConf(): TagAwareQueryResultCacheCategoryConf
    {
        return $this->tagAwareQueryResultCacheCategoryConf;
    }
}
