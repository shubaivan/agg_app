<?php


namespace App\DocumentRepository;

use App\Document\AwinProduct;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class AwinProductRepository
 * @package App\DocumentRepository
 * @method int getCount(Builder $builder)
 */
class AwinProductRepository extends ServiceDocumentRepository
{
    use CommonTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AwinProduct::class);
    }

    /**
     * @return int
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getCountDoc()
    {
        return $this->getCount($this->createQueryBuilder());
    }

    public function getListQuery()
    {
        $builder = $this->createQueryBuilder();
        return $builder->getQuery();
    }

    public function nativeFindOne()
    {
        $client = $this->getDocumentManager()->getClient();
        $collection = $client->symfony->AwinProduct;
        $cursor = $collection->findOne();

        return $cursor;
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

        $collection = $client->symfony->AwinProduct;

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


    /**
     * @param array $params
     * @return array
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getDataTable(array $params = [])
    {
        $filterCount = false;
        $client = $this->getDocumentManager()->getClient();
        $manager = $client->getManager();

        $columnIndex = $params['order'][0]['column']; // Column index
        $columnName = $params['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $params['order'][0]['dir']; // asc or desc
        $columnSortOrder = ($columnSortOrder == 'desc' ? -1 : 1);
        $prepareArray = [];
        $opt = [];
        $filter = [];
        if (isset($params['search']['value']) && strlen($params['search']['value'])) {
            $filter = ['$text' => ['$search' => $params['search']['value'], '$language' => 'sv']];
            $opt['sort']['score'] =
                 ['$meta' => 'textScore'];

            $sort_by_search = [
                'score' => ['$meta' => 'textScore']
            ];


            // Command
            $command = new \MongoDB\Driver\Command(
                [
                    "count" => "AwinProduct",
                    "query" => $filter
                ]
            );

            
            // Result
            $count = $manager->executeCommand(
                'symfony',
                $command
            );

            $toArray = $count->toArray();
            if (count($toArray)) {
                $resultCount = (array)array_shift($toArray);
                if(isset($resultCount['n'])){
                    $filterCount = $resultCount['n'];
                }
            }
        }
        if (isset($sort_by_search)) {
            $opt['sort'] = $sort_by_search;
        }

        if ($columnName) {
            $opt['sort'][$columnName] = $columnSortOrder;
        }

        if (isset($params['start'])) {
            $opt['skip'] = (int)$params['start'];
        }

        if (isset($params['length'])) {
            $opt['limit'] = (int)$params['length'];
        }


        $prepareArray['options'] = $opt;
        $prepareArray['filter'] = $filter;

        $collection = $client->symfony->AwinProduct;
        $cursor = $collection->find(
            $filter,
            $opt
        );
        
        return [
            'data' => $cursor->toArray(),
            'count' => $filterCount
        ];
    }

    public function nat()
    {
        $client = $this->getDocumentManager()->getClient();

        $connection = $client->getManager();

        $filter = ['$text' => ['$search' => "Sko Nike Classic Cortez för män -Svart", '$language' => 'sv']];


        $options = [
            'skip' => 2,
            'limit' => 2,
            'projection' => [
                'score' => ['$meta' => 'textScore']
            ],
            'sort' => [
                'score' => ['$meta' => 'textScore']
            ]
        ];

        $query = new \MongoDB\Driver\Query($filter, $options);
        $rows = $connection->executeQuery('symfony.AwinProduct', $query);
        return (array)$rows->toArray();
    }

    public function nat2($r = [])
    {
        $client = $this->getDocumentManager()->getClient();

        $collection = $client->symfony->AwinProduct;
        $cursor = $collection->find(
            ['$text' => ['$search' => 'Sko Nike Classic Cortez för män -Svart', '$language' => 'sv']],
            [
                'skip' => 2,
                'limit' => 2,
                'projection' => [
                    'score' => ['$meta' => 'textScore']
                ],
                'sort' => [
                    'score' => ['$meta' => 'textScore'], 'aw_product_id' => 1
                ]
            ]
        );

        $toArray1 = $cursor->toArray();

        return $toArray1;
    }
}