<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use App\DocumentRepository\AdtractionProductRepository;
use JMS\Serializer\Annotation;

/**
 * @MongoDB\Document(repositoryClass=AdtractionProductRepository::class)
 * @MongoDB\Index(keys={
 *     "Name"="text",
 *     "SKU"="text",
 *     "Description"="text",
 *     "Category"="text",
 *     "Price"="text",
 *     "Brand"="text",
 *     "Ean"="text",
 *     "shop"="text",
 *     "identityUniqData"="text",
 * })
 *
 * @MongoDB\UniqueIndex(keys={"Name"="asc", "SKU"="asc", "Brand"="asc", "Ean"="asc", "shop"="asc"})
 *
 * @Annotation\AccessorOrder("custom", custom = {
 *     "SKU",
 *     "ImageUrl",
 *     "decline",
 *     "declineReasonClass",
 *     "shop",
 *     "Brand",
 *     "Name",
 *     "Category",
 *     "Price",
 *     "Currency",
 *     "Instock"
 * })
 */
class AdtractionProduct extends AbstractDocument
{
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @MongoDB\Index()
     */
    private $SKU;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $Name;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $Description;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $Category;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $Price;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $Shipping;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $Currency;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $Instock;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $ProductUrl;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $ImageUrl;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $TrackingUrl;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $Brand;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $OriginalPrice;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $Ean;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $ManufacturerArticleNumber;
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $Extras;

    /**
     * AdtractionProduct constructor.
     * @param $SKU
     * @param $Name
     * @param $Description
     * @param $Category
     * @param $Price
     * @param $Shipping
     * @param $Currency
     * @param $Instock
     * @param $ProductUrl
     * @param $ImageUrl
     * @param $TrackingUrl
     * @param $Brand
     * @param $OriginalPrice
     * @param $Ean
     * @param $ManufacturerArticleNumber
     * @param $Extras
     * @param $shop
     * @param $identityUniqData
     */
    public function __construct(
        $SKU, $Name, $Description, $Category, $Price,
        $Shipping, $Currency, $Instock, $ProductUrl, $ImageUrl,
        $TrackingUrl, $Brand, $OriginalPrice, $Ean,
        $ManufacturerArticleNumber, $Extras, $shop, $identityUniqData
    )
    {
        $this->SKU = $SKU;
        $this->Name = $Name;
        $this->Description = $Description;
        $this->Category = $Category;
        $this->Price = $Price;
        $this->Shipping = $Shipping;
        $this->Currency = $Currency;
        $this->Instock = $Instock;
        $this->ProductUrl = $ProductUrl;
        $this->ImageUrl = $ImageUrl;
        $this->TrackingUrl = $TrackingUrl;
        $this->Brand = $Brand;
        $this->OriginalPrice = $OriginalPrice;
        $this->Ean = $Ean;
        $this->ManufacturerArticleNumber = $ManufacturerArticleNumber;
        $this->Extras = $Extras;
        $this->shop = $shop;
        $this->identityUniqData = $identityUniqData;
    }

    public static function getImageColumns(): array
    {
        return [
            'ImageUrl'
        ];
    }

    public static function getLinkColumns(): array
    {
        return [
            'ProductUrl', 'TrackingUrl'
        ];
    }

    public static function getShortPreviewText(): array
    {
        return [
            'id', 'SKU'
        ];
    }

    public static function convertToHtmColumns(): array
    {
        return [
            'Description'
        ];
    }

    public static function getSeparateFilterColumn(): array
    {
        return array_merge(['SKU', 'Brand'], parent::getSeparateFilterColumn());
    }
}