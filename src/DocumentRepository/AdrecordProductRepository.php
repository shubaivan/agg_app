<?php


namespace App\DocumentRepository;

use App\Document\AdrecordProduct;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class AdrecordProductRepository
 * @package App\DocumentRepository
 * @method int getCount(Builder $builder)
 */
class AdrecordProductRepository extends ServiceDocumentRepository
{
    use CommonTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdrecordProduct::class);
    }

    /**
     * @return int
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getCountDoc()
    {
        return $this->getCount($this->createQueryBuilder());
    }

    /**
     * @param array $params
     * @return array
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getDataTableAggr(array $params = [])
    {
        $filterCount = false;
        $client = $this->getDocumentManager()->getClient();

        $collection = $client->symfony->AdrecordProduct;

        $columnIndex = $params['order'][0]['column']; // Column index
        $columnName = $params['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $params['order'][0]['dir']; // asc or desc
        $columnSortOrder = ($columnSortOrder == 'desc' ? -1 : 1);
        $sort = [];

        $filter = [];
        $filterQuantity = [];

        if (isset($params['search']['value']) && strlen($params['search']['value'])) {
            $match = ['$match' => ['$text' => ['$search' => $params['search']['value']]]];
            array_push($filter, $match);
            array_push($filterQuantity, $match);

            $addFields = ['$addFields' => ['score' => ['$meta' => 'textScore']]];
            array_push($filter, $addFields);
            array_push($filterQuantity, $addFields);

            $addMatch = ['$match' => ['score' => ['$gt' => 1.0]]];
            array_push($filter, $addMatch);
            array_push($filterQuantity, $addMatch);

            $sort_by_search = [
                'score' => ['$meta' => 'textScore']
            ];
        }

        if (isset($params['columns']) && is_array($params['columns'])) {
            foreach ($params['columns'] as $column) {
                if (isset($column['search']['value'])
                    && isset($column['data'])
                    && strlen($column['search']['value'])
                ) {
                    $match = ['$match' => [
                        $column['data'] => $column['data'] == 'decline'
                            ? ($column['search']['value'] == 'true' ? true : false)
                            : $column['search']['value']]
                    ];
                    array_push($filter, $match);
                    array_push($filterQuantity, $match);

                }
            }
        }

        if (count($filterQuantity)) {
            $groupForCount = ['$group' => ['_id' => null, 'myCount' => ['$sum' => 1]]];
            $projectForCount = ['$project' => ['_id' => 0]];
            array_push($filterQuantity, $groupForCount);
            array_push($filterQuantity, $projectForCount);

            $cursorCount = $collection->aggregate(
                $filterQuantity,
                ["allowDiskUse" => true]
            );
            $toArray = $cursorCount->toArray();
            if (count($toArray)) {
                $array_shift = array_shift($toArray);
                if (isset($array_shift['myCount'])) {
                    $filterCount = $array_shift['myCount'];
                }
            }
        }

        if (isset($sort_by_search)) {
            $sort['$sort'] = $sort_by_search;
        }

        if ($columnName) {
            $sort['$sort'][$columnName] = $columnSortOrder;
        }

        array_push($filter, $sort);
        if (isset($params['start'])) {
            $skip = ['$skip' => (int)$params['start']];
            array_push($filter, $skip);
        }

        if (isset($params['length'])) {
            $limit = ['$limit' => (int)$params['length']];
            array_push($filter, $limit);
        }

        $cursor = $collection->aggregate(
            $filter,
            ["allowDiskUse" => true]
        );

        return [
            'data' => $cursor->toArray(),
            'count' => $filterCount
        ];
    }    
}