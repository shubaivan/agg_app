<?php


namespace App\DocumentRepository;

use App\Document\AdrecordProduct;
use App\QueueModel\ResourceProductQueues;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class AdrecordProductRepository
 * @package App\DocumentRepository
 * @method int getCount(Builder $builder)
 * @method array getDataTableAggr ($collection, array $params)
 * @method string|null matchExistProductAllow (string $collection, array $match)
 */
class AdrecordProductRepository extends ServiceDocumentRepository implements CarefulSavingSku
{
    use CommonTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdrecordProduct::class);
    }

    /**
     * @param ResourceProductQueues $productQueues
     * @return mixed|string|null
     */
    public function matchExistProduct(ResourceProductQueues $productQueues)
    {
        return  $this->matchExistProductAllow('AdrecordProduct', [
            'identityUniqData' => $productQueues->getAttributeByName('identityUniqData'),
        ]);
    }

    /**
     * @return int
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getCountDoc()
    {
        return $this->getCount($this->createQueryBuilder());
    }

    public function createProduct(ResourceProductQueues $productQueues, string $shop)
    {
        /**
         * @var $name
         * @var $category
         * @var $SKU
         * @var $EAN
         * @var $description
         * @var $model
         * @var $brand
         * @var $price
         * @var $shippingPrice
         * @var $currency
         * @var $productUrl
         * @var $graphicUrl
         * @var $inStock
         * @var $inStockQty
         * @var $deliveryTime
         * @var $regularPrice
         * @var $gender
         * @var $identityUniqData
         */
        extract($productQueues->getRow());

        $adrecordProduct = new AdrecordProduct(
            $name, $category, $SKU, $EAN, $description,
            $model, $brand, $price, $shippingPrice, $currency,
            $productUrl, $graphicUrl, $inStock, $inStockQty, $deliveryTime,
            $regularPrice, $gender, $shop, $identityUniqData
        );
        $this->dm->persist($adrecordProduct);
    }
}