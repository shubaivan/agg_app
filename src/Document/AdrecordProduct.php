<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use App\DocumentRepository\AdrecordProductRepository;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document(repositoryClass=AdrecordProductRepository::class)
 * @MongoDB\Index(keys={
 *     "name"="text",
 *     "SKU"="text",
 *     "description"="text",
 *     "category"="text",
 *     "price"="text",
 *     "brand"="text",
 *     "identityUniqData"="text",
 * })
 *
 * @MongoDB\UniqueIndex(keys={"name"="asc", "SKU"="asc", "brand"="asc", "EAN"="asc", "shop"="asc"})
 *
 * @Annotation\AccessorOrder("custom", custom = {
 *     "SKU",
 *     "graphicUrl",
 *     "decline",
 *     "declineReasonClass",
 *     "shop",
 *     "brand",
 *     "name",
 *     "category",
 *     "price"
 * })
 * @MongoDB\HasLifecycleCallbacks()
 */
class AdrecordProduct extends AbstractDocument implements DataTableInterface
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
    private $category;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @MongoDB\Index()
     * @Assert\NotBlank()
     */
    private $SKU;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $EAN;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $description;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $model;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $brand;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $price;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $shippingPrice;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $currency;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $productUrl;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $graphicUrl;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $inStock;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $inStockQty;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $deliveryTime;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $regularPrice;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    private $gender;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return AdrecordProduct
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return AdrecordProduct
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     * @return AdrecordProduct
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSKU()
    {
        return $this->SKU;
    }

    /**
     * @param mixed $SKU
     * @return AdrecordProduct
     */
    public function setSKU($SKU)
    {
        $this->SKU = $SKU;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEAN()
    {
        return $this->EAN;
    }

    /**
     * @param mixed $EAN
     * @return AdrecordProduct
     */
    public function setEAN($EAN)
    {
        $this->EAN = $EAN;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return AdrecordProduct
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     * @return AdrecordProduct
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param mixed $brand
     * @return AdrecordProduct
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     * @return AdrecordProduct
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingPrice()
    {
        return $this->shippingPrice;
    }

    /**
     * @param mixed $shippingPrice
     * @return AdrecordProduct
     */
    public function setShippingPrice($shippingPrice)
    {
        $this->shippingPrice = $shippingPrice;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     * @return AdrecordProduct
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductUrl()
    {
        return $this->productUrl;
    }

    /**
     * @param mixed $productUrl
     * @return AdrecordProduct
     */
    public function setProductUrl($productUrl)
    {
        $this->productUrl = $productUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGraphicUrl()
    {
        return $this->graphicUrl;
    }

    /**
     * @param mixed $graphicUrl
     * @return AdrecordProduct
     */
    public function setGraphicUrl($graphicUrl)
    {
        $this->graphicUrl = $graphicUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInStock()
    {
        return $this->inStock;
    }

    /**
     * @param mixed $inStock
     * @return AdrecordProduct
     */
    public function setInStock($inStock)
    {
        $this->inStock = $inStock;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInStockQty()
    {
        return $this->inStockQty;
    }

    /**
     * @param mixed $inStockQty
     * @return AdrecordProduct
     */
    public function setInStockQty($inStockQty)
    {
        $this->inStockQty = $inStockQty;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeliveryTime()
    {
        return $this->deliveryTime;
    }

    /**
     * @param mixed $deliveryTime
     * @return AdrecordProduct
     */
    public function setDeliveryTime($deliveryTime)
    {
        $this->deliveryTime = $deliveryTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRegularPrice()
    {
        return $this->regularPrice;
    }

    /**
     * @param mixed $regularPrice
     * @return AdrecordProduct
     */
    public function setRegularPrice($regularPrice)
    {
        $this->regularPrice = $regularPrice;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     * @return AdrecordProduct
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    public static function getImageColumns(): array
    {
        return [
            'graphicUrl'
        ];
    }

    public static function getLinkColumns(): array
    {
        return [
            'productUrl'
        ];
    }

    public static function getShortPreviewText(): array
    {
        return [
            'id'
        ];
    }

    public static function convertToHtmColumns(): array
    {
        return [
            'description'
        ];
    }

    public static function getSeparateFilterColumn(): array
    {
        return array_merge(['SKU', 'brand'], parent::getSeparateFilterColumn());
    }

    public static function arrayColumns(): array
    {
        return [];
    }
}