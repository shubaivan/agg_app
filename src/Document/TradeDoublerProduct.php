<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation;
use App\DocumentRepository\TradeDoublerProductRepository;

/**
 * @MongoDB\Document(repositoryClass=TradeDoublerProductRepository::class)
 * @MongoDB\Index(keys={
 *     "TDProductId"="text",
 *     "name"="text",
 *     "categories"="text",
 *     "MerchantCategoryName"="text",
 *     "description"="text",
 *     "price"="text",
 *     "brand"="text",
 *     "sku"="text",
 *     "model"="text",
 *     "declineReasonClass"="text",
 *     "shop"="text",
 *     "identityUniqData"="text",
 * })
 *
 * @MongoDB\UniqueIndex(keys={"name"="asc", "sku"="asc", "brand"="asc", "ean"="asc", "shop"="asc", "tradeDoublerId"="asc"})
 * 
 * @Annotation\AccessorOrder("custom", custom = {
 *     "TDProductId",
 *     "imageUrl",
 *     "decline",
 *     "declineReasonClass",
 *     "shop",
 *     "brand",
 *     "categories",
 *     "MerchantCategoryName",
 *     "price"
 * })
 */
class TradeDoublerProduct extends AbstractDocument implements DataTableInterface
{
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $name;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $productImage;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $productUrl;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $imageUrl;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $height;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $width;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $categories;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $MerchantCategoryName;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $TDCategoryName;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $TDCategoryId;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $TDProductId;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $description;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $feedId;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $groupingId;

    /**
     * @MongoDB\Field(type="string")
     * @MongoDB\Index()
     * @Annotation\Type("string")
     */
    private $tradeDoublerId;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $productLanguage;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $modified;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $price;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $currency;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $programName;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $availability;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $brand;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $condition;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $deliveryTime;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $ean;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $upc;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $isbn;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $mpn;

    /**
     * @MongoDB\Field(type="string")
     * @MongoDB\Index()
     * @Annotation\Type("string")
     */
    private $sku;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $identifiers;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $inStock;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $manufacturer;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $model;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $programLogo;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $promoText;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $shippingCost;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $shortDescription;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $size;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $fields;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $warranty;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $weight;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $techSpecs;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $dateformat;

    /**
     * TradeDoublerProduct constructor.
     * @param $name
     * @param $productImage
     * @param $productUrl
     * @param $imageUrl
     * @param $height
     * @param $width
     * @param $categories
     * @param $MerchantCategoryName
     * @param $TDCategoryName
     * @param $TDCategoryId
     * @param $TDProductId
     * @param $description
     * @param $feedId
     * @param $groupingId
     * @param $tradeDoublerId
     * @param $productLanguage
     * @param $modified
     * @param $price
     * @param $currency
     * @param $programName
     * @param $availability
     * @param $brand
     * @param $condition
     * @param $deliveryTime
     * @param $ean
     * @param $upc
     * @param $isbn
     * @param $mpn
     * @param $sku
     * @param $identifiers
     * @param $inStock
     * @param $manufacturer
     * @param $model
     * @param $programLogo
     * @param $promoText
     * @param $shippingCost
     * @param $shortDescription
     * @param $size
     * @param $fields
     * @param $warranty
     * @param $weight
     * @param $techSpecs
     * @param $dateformat
     * @param $shop
     * @param $identityUniqData
     */
    public function __construct(
        $name, $productImage, $productUrl, $imageUrl, $height, $width,
        $categories, $MerchantCategoryName, $TDCategoryName, $TDCategoryId, 
        $TDProductId, $description, $feedId, $groupingId,
        $tradeDoublerId, $productLanguage, $modified, $price, 
        $currency, $programName, $availability, $brand, $condition,
        $deliveryTime, $ean, $upc, $isbn, $mpn, $sku, $identifiers,
        $inStock, $manufacturer, $model, $programLogo, $promoText,
        $shippingCost, $shortDescription, $size, $fields, $warranty,
        $weight, $techSpecs, $dateformat, $shop, $identityUniqData
    )
    {
        $this->name = $name;
        $this->productImage = $productImage;
        $this->productUrl = $productUrl;
        $this->imageUrl = $imageUrl;
        $this->height = $height;
        $this->width = $width;
        $this->categories = $categories;
        $this->MerchantCategoryName = $MerchantCategoryName;
        $this->TDCategoryName = $TDCategoryName;
        $this->TDCategoryId = $TDCategoryId;
        $this->TDProductId = $TDProductId;
        $this->description = $description;
        $this->feedId = $feedId;
        $this->groupingId = $groupingId;
        $this->tradeDoublerId = $tradeDoublerId;
        $this->productLanguage = $productLanguage;
        $this->modified = $modified;
        $this->price = $price;
        $this->currency = $currency;
        $this->programName = $programName;
        $this->availability = $availability;
        $this->brand = $brand;
        $this->condition = $condition;
        $this->deliveryTime = $deliveryTime;
        $this->ean = $ean;
        $this->upc = $upc;
        $this->isbn = $isbn;
        $this->mpn = $mpn;
        $this->sku = $sku;
        $this->identifiers = $identifiers;
        $this->inStock = $inStock;
        $this->manufacturer = $manufacturer;
        $this->model = $model;
        $this->programLogo = $programLogo;
        $this->promoText = $promoText;
        $this->shippingCost = $shippingCost;
        $this->shortDescription = $shortDescription;
        $this->size = $size;
        $this->fields = $fields;
        $this->warranty = $warranty;
        $this->weight = $weight;
        $this->techSpecs = $techSpecs;
        $this->dateformat = $dateformat;
        $this->shop = $shop;
        $this->identityUniqData = $identityUniqData;
    }

    public static function getImageColumns(): array
    {
        return ['imageUrl', 'productImage', 'programLogo'];
    }

    public static function getLinkColumns(): array
    {
        return ['productUrl'];
    }

    public static function getShortPreviewText(): array
    {
        return [
            'id'
        ];
    }

    public static function convertToHtmColumns(): array
    {
        return ['description'];    
    }

    public static function getSeparateFilterColumn(): array
    {
        return array_merge(['TDProductId'], parent::getSeparateFilterColumn());
    }
}