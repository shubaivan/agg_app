<?php


namespace App\DocumentRepository;

use App\Document\AdtractionProduct;
use App\QueueModel\ResourceProductQueues;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class AdtractionProductRepository
 * @package App\DocumentRepository
 * @method int getCount(Builder $builder)
 * @method array getDataTableAggr ($collection, array $params)
 */
class AdtractionProductRepository extends ServiceDocumentRepository implements CarefulSavingSku
{
    use CommonTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdtractionProduct::class);
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
            'SKU' => $productQueues->getSku(),
            'Brand' => $productQueues->getBrand(),
            'Ean' => $productQueues->getEan(),
            'shop' => $productQueues->getShop(),
        ]);
    }
    
    public function createProduct(
        ResourceProductQueues $productQueues,
        $shop
    )
    {
        /**
         * @var $SKU
         * @var $Name
         * @var $Description
         * @var $Category
         * @var $Price
         * @var $Shipping
         * @var $Currency
         * @var $Instock
         * @var $ProductUrl
         * @var $ImageUrl
         * @var $TrackingUrl
         * @var $Brand
         * @var $OriginalPrice
         * @var $Ean
         * @var $ManufacturerArticleNumber
         * @var $Extras
         * @var $identityUniqData
         */
        extract($productQueues->getRow());

        $adrecordProduct = new AdtractionProduct(
            $SKU, $Name, $Description, $Category, $Price,
            $Shipping, $Currency, $Instock, $ProductUrl, $ImageUrl,
            $TrackingUrl, $Brand, $OriginalPrice, $Ean,
            $ManufacturerArticleNumber, $Extras, $shop, $identityUniqData
        );
        
        $this->dm->persist($adrecordProduct);
    }
}