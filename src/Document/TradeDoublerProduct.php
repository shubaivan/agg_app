<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation;
use App\DocumentRepository\TradeDoublerProductRepository;
use Symfony\Component\Validator\Constraints as Assert;

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
 *     "gFields"="text"
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
 *
 * @MongoDB\HasLifecycleCallbacks()
 */
class TradeDoublerProduct extends AbstractDocument implements DataTableInterface
{
    const GROUP_GET_TH = 'trade_doubler_product_group_get_th';

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $name;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $productImage;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Assert\NotBlank()
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $productUrl;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $imageUrl;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $height;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $width;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $categories;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $MerchantCategoryName;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $TDCategoryName;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $TDCategoryId;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $TDProductId;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $description;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $feedId;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $groupingId;

    /**
     * @MongoDB\Field(type="string")
     * @MongoDB\Index()
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $tradeDoublerId;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $productLanguage;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $modified;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $price;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $currency;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $programName;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $availability;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $brand;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $condition;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $deliveryTime;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $ean;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $upc;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $isbn;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $mpn;

    /**
     * @MongoDB\Field(type="string")
     * @MongoDB\Index()
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $sku;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Assert\NotBlank()
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $identifiers;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $inStock;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $manufacturer;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $model;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $programLogo;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $promoText;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $shippingCost;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $shortDescription;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $size;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $fields;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $warranty;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $weight;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $techSpecs;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $dateformat;

    /**
     * @MongoDB\Field(type="hash")
     * @Annotation\Type("array")
     * @Annotation\Groups({TradeDoublerProduct::GROUP_GET_TH})
     */
    private $gFieldsShow = [];

    /**
     * @MongoDB\Field(type="collection")
     * @Annotation\Type("array")
     */
    private $gFields = [];

    public static function arrayColumns(): array
    {
        return ['gFieldsShow'];
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