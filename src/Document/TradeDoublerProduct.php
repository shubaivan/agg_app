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
 *     "shop"="text"
 * })
 * @Annotation\AccessorOrder("custom", custom = {
 *     "TDProductId",
 *     "productImage",
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
    private $name;
    private $productImage;
    private $productUrl;
    private $imageUrl;
    private $height;
    private $width;
    private $categories;
    private $MerchantCategoryName;
    private $TDCategoryName;
    private $TDCategoryId;
    private $TDProductId;
    private $description;
    private $feedId;
    private $groupingId;
    private $tradeDoublerId;
    private $productLanguage;
    private $modified;
    private $price;
    private $currency;
    private $programName;
    private $availability;
    private $brand;
    private $condition;
    private $deliveryTime;
    private $ean;
    private $upc;
    private $isbn;
    private $mpn;
    private $sku;
    private $identifiers;
    private $inStock;
    private $manufacturer;
    private $model;
    private $programLogo;
    private $promoText;
    private $shippingCost;
    private $shortDescription;
    private $size;
    private $fields;
    private $warranty;
    private $weight;
    private $techSpecs;
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
     */
    public function __construct($name, $productImage, $productUrl, $imageUrl, $height, $width, $categories, $MerchantCategoryName, $TDCategoryName, $TDCategoryId, $TDProductId, $description, $feedId, $groupingId, $tradeDoublerId, $productLanguage, $modified, $price, $currency, $programName, $availability, $brand, $condition, $deliveryTime, $ean, $upc, $isbn, $mpn, $sku, $identifiers, $inStock, $manufacturer, $model, $programLogo, $promoText, $shippingCost, $shortDescription, $size, $fields, $warranty, $weight, $techSpecs, $dateformat, $shop)
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
            'id', 'sku'
        ];
    }

    public static function convertToHtmColumns(): array
    {
        return ['description'];    
    }
}