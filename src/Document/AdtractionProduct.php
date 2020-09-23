<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use App\DocumentRepository\AdtractionProductRepository;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

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
 *
 * @MongoDB\HasLifecycleCallbacks()
 */
class AdtractionProduct extends AbstractDocument
{
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
            'id'
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

    public static function arrayColumns(): array
    {
        return [];
    }
}