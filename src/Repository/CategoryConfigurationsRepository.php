<?php

namespace App\Repository;

use App\Entity\CategoryConfigurations;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Cache\ResultCacheStatement;

/**
 * @method CategoryConfigurations|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryConfigurations|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryConfigurations[]    findAll()
 * @method CategoryConfigurations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryConfigurationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryConfigurations::class);
    }

    public function matchSizeCategories(array $sizes, array $ids)
    {
        $params = [];
        $types = [];
        foreach ($ids as $key=>$id) {
            if (isset($id['id'])) {
                $params[':main_id' . $key] = $id['id'];
                $types[':main_id' . $key] = \PDO::PARAM_INT;
            }
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
}
