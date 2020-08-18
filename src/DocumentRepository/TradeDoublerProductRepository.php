<?php


namespace App\DocumentRepository;


use App\Document\AwinProduct;
use App\Document\TradeDoublerProduct;
use App\QueueModel\ResourceProductQueues;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class TradeDoublerProductRepository
 * @package App\DocumentRepository
 *
 * @package App\DocumentRepository
 * @method int getCount(Builder $builder)
 */
class TradeDoublerProductRepository extends ServiceDocumentRepository implements CarefulSavingSku
{
    use CommonTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TradeDoublerProduct::class);
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
     * @param string $sku
     * @return object|null
     */
    public function matchExistProduct(string $sku)
    {
        return $this->findOneBy(['sku' => $sku]);
    }

    public function getListQuery()
    {
        $builder = $this->createQueryBuilder();
        return $builder->getQuery();
    }

    public function nativeFindOne()
    {
        $client = $this->getDocumentManager()->getClient();
        $collection = $client->symfony->TradeDoublerProduct;
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

        $collection = $client->symfony->TradeDoublerProduct;

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
                if (isset($resultCount['n'])) {
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

        $collection = $client->symfony->TradeDoublerProduct;
        $cursor = $collection->find(
            $filter,
            $opt
        );

        return [
            'data' => $cursor->toArray(),
            'count' => $filterCount
        ];
    }
    
    public function createProduct(ResourceProductQueues $productQueues, string $shop)
    {
        /**
         * @var $name
         * @var $productImage
         * @var $productUrl
         * @var $imageUrl
         * @var $height
         * @var $width
         * @var $categories
         * @var $MerchantCategoryName
         * @var $TDCategoryName
         * @var $TDCategoryId
         * @var $TDProductId
         * @var $description
         * @var $feedId
         * @var $groupingId
         * @var $tradeDoublerId
         * @var $productLanguage
         * @var $modified
         * @var $price
         * @var $currency
         * @var $programName
         * @var $availability
         * @var $brand
         * @var $condition
         * @var $deliveryTime
         * @var $ean
         * @var $upc
         * @var $isbn
         * @var $mpn
         * @var $sku
         * @var $identifiers
         * @var $inStock
         * @var $manufacturer
         * @var $model
         * @var $programLogo
         * @var $promoText
         * @var $shippingCost
         * @var $shortDescription
         * @var $size
         * @var $fields
         * @var $warranty
         * @var $weight
         * @var $techSpecs
         * @var $dateformat
         */
        extract($productQueues->getRow());

        $tradeDoublerProduct = new TradeDoublerProduct(
            $name, $productImage, $productUrl, $imageUrl, $height, 
            $width, $categories, $MerchantCategoryName, $TDCategoryName, 
            $TDCategoryId, $TDProductId, $description, $feedId, 
            $groupingId, $tradeDoublerId, $productLanguage, $modified, $price, 
            $currency, $programName, $availability, $brand, $condition, 
            $deliveryTime, $ean, $upc, $isbn, $mpn, $sku, 
            $identifiers, $inStock, $manufacturer, $model, $programLogo, 
            $promoText, $shippingCost, $shortDescription, $size, $fields, 
            $warranty, $weight, $techSpecs, $dateformat, $shop
        );
        
        $this->dm->persist($tradeDoublerProduct);
    }
}