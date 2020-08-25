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
 * @method array getDataTableAggr (string $collection, array $params)
 * @method string|null matchExistProductAllow (string $collection, array $match)
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
     * @param ResourceProductQueues $productQueues
     * @return mixed|string|null
     */
    public function matchExistProduct(ResourceProductQueues $productQueues)
    {
        return  $this->matchExistProductAllow('TradeDoublerProduct', [
            'identityUniqData' => $productQueues->getAttributeByName('identityUniqData'),
        ]);
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
         * @var $identityUniqData
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
            $warranty, $weight, $techSpecs, $dateformat, $shop, $identityUniqData
        );

        $this->dm->persist($tradeDoublerProduct);
    }
}