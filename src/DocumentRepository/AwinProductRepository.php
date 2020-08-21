<?php


namespace App\DocumentRepository;

use App\Document\AwinProduct;
use App\QueueModel\ResourceProductQueues;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class AwinProductRepository
 * @package App\DocumentRepository
 * @method int getCount(Builder $builder)
 * @method array getDataTableAggr ($collection, array $params)
 */
class AwinProductRepository extends ServiceDocumentRepository implements CarefulSavingSku
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

    /**
     * @param string $sku
     * @return object|null
     */
    public function matchExistProduct(ResourceProductQueues $productQueues)
    {
        return $this->findOneBy([
            'aw_product_id' => $productQueues->getSku(),
            'brand_name' => $productQueues->getBrand(),
            'ean' => $productQueues->getEan(),
            'shop' => $productQueues->getShop(),
        ]);
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

    public function dictDeclineError(string $collectionName)
    {
//        db.getCollection('AwinProduct').distinct("declineReasonClass",
// { declineReasonClass: { $ne: '' } })
        $client = $this->getDocumentManager()->getClient();
        $collection = $client->symfony->$collectionName;

        return $collection->distinct(
            'declineReasonClass',
            [
                "declineReasonClass" => ['$ne' => '']
            ]
        );
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

    public function createProduct(ResourceProductQueues $productQueues, string $shop)
    {
        /**
         * @var $aw_deep_link
         * @var $product_name
         * @var $aw_product_id
         * @var $merchant_product_id
         * @var $merchant_image_url
         * @var $description
         * @var $merchant_category
         * @var $search_price
         * @var $merchant_name
         * @var $merchant_id
         * @var $category_name
         * @var $category_id
         * @var $aw_image_url
         * @var $currency
         * @var $store_price
         * @var $delivery_cost
         * @var $merchant_deep_link
         * @var $language
         * @var $last_updated
         * @var $display_price
         * @var $data_feed_id
         * @var $brand_name
         * @var $brand_id
         * @var $colour
         * @var $product_short_description
         * @var $specifications
         * @var $condition
         * @var $product_model
         * @var $model_number
         * @var $dimensions
         * @var $keywords
         * @var $promotional_text
         * @var $product_type
         * @var $commission_group
         * @var $merchant_product_category_path
         * @var $merchant_product_second_category
         * @var $merchant_product_third_category
         * @var $rrp_price
         * @var $saving
         * @var $savings_percent
         * @var $base_price
         * @var $base_price_amount
         * @var $base_price_text
         * @var $product_price_old
         * @var $delivery_restrictions
         * @var $delivery_weight
         * @var $warranty
         * @var $terms_of_contract
         * @var $delivery_time
         * @var $in_stock
         * @var $stock_quantity
         * @var $valid_from
         * @var $valid_to
         * @var $is_for_sale
         * @var $web_offer
         * @var $pre_order
         * @var $stock_status
         * @var $size_stock_status
         * @var $size_stock_amount
         * @var $merchant_thumb_url
         * @var $large_image
         * @var $alternate_image
         * @var $aw_thumb_url
         * @var $alternate_image_two
         * @var $alternate_image_three
         * @var $alternate_image_four
         * @var $ean
         * @var $isbn
         * @var $upc
         * @var $mpn
         * @var $parent_product_id
         * @var $product_GTIN
         * @var $basket_link
         * @var $Fashion_suitable_for
         * @var $Fashion_category
         * @var $Fashion_size
         * @var $Fashion_material
         * @var $Fashion_pattern
         * @var $Fashion_swatch
         * @var $identityUniqData
         */
        extract($productQueues->getRow());

        $adrecordProduct = new AwinProduct(
            $aw_deep_link, $product_name, $aw_product_id, $merchant_product_id,
            $merchant_image_url, $description, $merchant_category, $search_price,
            $merchant_name, $merchant_id, $category_name, $category_id, $aw_image_url,
            $currency, $store_price, $delivery_cost, $merchant_deep_link, $language,
            $last_updated, $display_price, $data_feed_id, $brand_name, $brand_id,
            $colour, $product_short_description, $specifications, $condition,
            $product_model, $model_number, $dimensions, $keywords, $promotional_text,
            $product_type, $commission_group, $merchant_product_category_path,
            $merchant_product_second_category, $merchant_product_third_category, $rrp_price,
            $saving, $savings_percent, $base_price, $base_price_amount, $base_price_text,
            $product_price_old, $delivery_restrictions, $delivery_weight, $warranty,
            $terms_of_contract, $delivery_time, $in_stock, $stock_quantity, $valid_from,
            $valid_to, $is_for_sale, $web_offer, $pre_order, $stock_status,
            $size_stock_status, $size_stock_amount, $merchant_thumb_url, $large_image,
            $alternate_image, $aw_thumb_url, $alternate_image_two, $alternate_image_three,
            $alternate_image_four, $ean, $isbn, $upc, $mpn, $parent_product_id,
            $product_GTIN, $basket_link, $Fashion_suitable_for, $Fashion_category,
            $Fashion_size, $Fashion_material, $Fashion_pattern, 
            $Fashion_swatch, $shop, $identityUniqData
        );
        $this->dm->persist($adrecordProduct);
    }
}