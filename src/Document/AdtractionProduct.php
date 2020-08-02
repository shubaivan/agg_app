<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class AdtractionProduct extends AbstractDocument
{
    /**
     * @MongoDB\Field(type="string")
     */
    private $SKU;
    /**
     * @MongoDB\Field(type="string")
     */
    private $Name;
    /**
     * @MongoDB\Field(type="string")
     */
    private $Description;
    /**
     * @MongoDB\Field(type="string")
     */
    private $Category;
    /**
     * @MongoDB\Field(type="string")
     */
    private $Price;
    /**
     * @MongoDB\Field(type="string")
     */
    private $Shipping;
    /**
     * @MongoDB\Field(type="string")
     */
    private $Currency;
    /**
     * @MongoDB\Field(type="string")
     */
    private $Instock;
    /**
     * @MongoDB\Field(type="string")
     */
    private $ProductUrl;
    /**
     * @MongoDB\Field(type="string")
     */
    private $ImageUrl;
    /**
     * @MongoDB\Field(type="string")
     */
    private $TrackingUrl;
    /**
     * @MongoDB\Field(type="string")
     */
    private $Brand;
    /**
     * @MongoDB\Field(type="string")
     */
    private $OriginalPrice;
    /**
     * @MongoDB\Field(type="string")
     */
    private $Ean;
    /**
     * @MongoDB\Field(type="string")
     */
    private $ManufacturerArticleNumber;
    /**
     * @MongoDB\Field(type="string")
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
     */
    public function __construct(
        $SKU, $Name, $Description, $Category, $Price, 
        $Shipping, $Currency, $Instock, $ProductUrl, $ImageUrl, 
        $TrackingUrl, $Brand, $OriginalPrice, $Ean, 
        $ManufacturerArticleNumber, $Extras, $shop
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
    }


}