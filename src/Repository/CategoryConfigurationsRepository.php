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
 * @method ParameterBag handleDataTablesRequest(array $params)
 */
class CategoryConfigurationsRepository extends ServiceEntityRepository
{
    use DataTablesApproachRepository;

    const CATEGORY_CONF_SEARCH = 'category_conf_search';
    const SUB_CATEGORIES_ID = 'sub_categories_id';
    const CATEGORY_CONF_SEARCH_CONT = "category_conf_search_cont";
    const CATEGORY_CONF_SEARCH_SUB_COUNT = 'category_conf_search_sub_count';
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
        $parameterBag = $this->handleDataTablesRequest($params);
        if (isset($params[self::SUB_CATEGORIES_ID])) {
            $parameterBag->set(self::SUB_CATEGORIES_ID, $params[self::SUB_CATEGORIES_ID]);
        }

        if (isset($params['level'])) {
            $parameterBag->set('level', $params['level']);
        }

        $categoryConfNativeData = $this->getCategoryConfNative(
            $parameterBag, $count, $total
        );
        if (!$count) {
            $catIds = array_map(function ($v) {
                return $v['id'] ?? '';
            }, $categoryConfNativeData);

            if ($catIds) {
                $categoryConfNativeDataSubCount = $this->
                getCategoryConfNativeSubCategoriesCount(
                    $catIds
                );

                $categoryConfNativeData = array_map(function ($catData) use ($categoryConfNativeDataSubCount){
                    $array_filter = array_filter($categoryConfNativeDataSubCount, function ($v) use ($catData) {
                        return $catData['id'] == $v['id'];
                    });
                    if ($array_filter) {
                        $catData['sub_count'] = array_shift($array_filter)['count'];
                    }

                    return $catData;
                }, $categoryConfNativeData);
            }

        }

        return $categoryConfNativeData;
    }

    /**
     * @param array $catIds
     * @return mixed[]
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function getCategoryConfNativeSubCategoriesCount(
        array $catIds
    )
    {
        $connection = $this->getEntityManager()->getConnection();
        $params = [];
        $types = [];
        $query = '
            SELECT                         
            cat.id,                       
            COUNT(cat_rel.id)
            
            FROM category AS cat
            LEFT JOIN category_relations AS cat_rel ON cat_rel.main_category_id = cat.id											  
        ';

        $ids = array_combine(
            array_map(function ($key) {
                return ':var_id' . $key;
            }, array_keys($catIds)),
            array_values($catIds)
        );
        $params = array_merge($ids, $params);
        $types = array_merge(array_map(function ($v) {
            return \PDO::PARAM_INT;
        }, $ids), $types);
        $bindKeysIds = implode(',', array_keys($ids));
        $query .= "                           
            WHERE cat.id IN ($bindKeysIds)
            GROUP BY cat.id
        ";

        $this->getTagAwareQueryResultCacheCategoryConf()->setQueryCacheTags(
            $query,
            $params,
            $types,
            [self::CATEGORY_CONF_SEARCH_SUB_COUNT],
            0,
            self::CATEGORY_CONF_SEARCH_SUB_COUNT
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

        $fetchResult = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $statement->closeCursor();

        return $fetchResult;

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
                        category_alias.position as "CategoryPosition",
                        \'Edit,Sub Categories\' as "Action"
            ';
        }

        $query .= '
                FROM category_configurations AS c_conf
                INNER JOIN category AS category_alias ON category_alias.id = c_conf.category_id_id
                WHERE category_alias.custome_category = :custome_category 
        ';

        switch ($parameterBag->get('level')):
            case '1':
                $query .= '                
                AND 
                EXISTS(SELECT 1 FROM category_relations WHERE main_category_id = category_alias.id)
                AND
                NOT EXISTS(SELECT 1 FROM category_relations WHERE sub_category_id = category_alias.id)';
                break;
            case '2':
                $query .= '                
                AND 
                EXISTS(SELECT 1 FROM category_relations WHERE main_category_id = category_alias.id)
                AND
                EXISTS(SELECT 1 FROM category_relations WHERE sub_category_id = category_alias.id)';
                break;
            case '3':
                $query .= '                
                AND 
                NOT EXISTS(SELECT 1 FROM category_relations WHERE main_category_id = category_alias.id)
                AND
                EXISTS(SELECT 1 FROM category_relations WHERE sub_category_id = category_alias.id)';
                break;
            default:
        endswitch;

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
            && !$total
        ) {
            $varCategoryName = (bool)$parameterBag->get('CategoryName');
            $query .= '
                AND category_alias.hot_category = :hot_category
            ';
            $params[':hot_category'] = $varCategoryName;
            $types[':hot_category'] = \PDO::PARAM_BOOL;
        }

        if ($parameterBag->get(self::SUB_CATEGORIES_ID)
            && is_array($parameterBag->get(self::SUB_CATEGORIES_ID))
        ) {
            $ids = array_combine(
                array_map(function ($key) {
                    return ':var_id' . $key;
                }, array_keys($parameterBag->get(self::SUB_CATEGORIES_ID))),
                array_values($parameterBag->get(self::SUB_CATEGORIES_ID))
            );
            $params = array_merge($ids, $params);
            $types = array_merge(array_map(function ($v) {
                return \PDO::PARAM_INT;
            }, $ids), $types);
            $bindKeysIds = implode(',', array_keys($ids));
            $query .= "                           
                AND category_alias.id IN ($bindKeysIds)
            ";
        }

        if (!$count) {

            if ($parameterBag->get('sort_by') === false) {
                $query .= '
                    ORDER BY category_alias.id
                ';
            } else {
                $query .= '
                    ORDER BY "' . $parameterBag->get('sort_by') . '" ' . mb_strtoupper($parameterBag->get('sort_order')) . '
                ';
            }

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
            0, $count ? self::CATEGORY_CONF_SEARCH_CONT : self::CATEGORY_CONF_SEARCH
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
            $fetchResult = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $fetchResult = isset($fetchResult[0]['count']) ? (int)$fetchResult[0]['count'] : 0;
        } else {
            $fetchResult = $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
        $statement->closeCursor();

        return $fetchResult;
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
        foreach ($sizes as $key => $size) {
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

        foreach ($ids as $key => $id) {
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
                cr.main_category_id IN (' . $idsMain . ')';

        $sizeCondStr = implode(' OR ', $sizesCond);

        $query .= '
            AND (' . $sizeCondStr . ')
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
