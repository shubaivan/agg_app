<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use App\DocumentRepository\AwinProductRepository;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document(repositoryClass=AwinProductRepository::class)
 * @MongoDB\Index(keys={
 *     "aw_product_id"="text",
 *     "product_name"="text",
 *     "merchant_category"="text",
 *     "description"="text",
 *     "merchant_name"="text",
 *     "category_name"="text",
 *     "brand_name"="text",
 *     "colour"="text",
 *     "product_short_description"="text",
 *     "merchant_product_category_path"="text",
 *     "Fashion_category"="text",
 *     "Fashion_size"="text",
 *     "declineReasonClass"="text",
 *     "shop"="text",
 *     "identityUniqData"="text",
 * })
 *
 * @MongoDB\UniqueIndex(keys={"product_name"="asc", "aw_product_id"="asc", "brand_name"="asc", "ean"="asc", "shop"="asc"})
 *
 * @Annotation\AccessorOrder("custom", custom = {
 *     "aw_product_id",
 *     "merchant_image_url",
 *     "decline",
 *     "declineReasonClass",
 *     "shop",
 *     "brand_name",
 *     "aw_deep_link",
 *     "product_name",
 *     "search_price"
 * })
 *
 * @MongoDB\HasLifecycleCallbacks()
 */
class AwinProduct extends AbstractDocument
{
    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @Assert\NotBlank()
     */
    protected $aw_deep_link;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $product_name;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     * @MongoDB\Index()
     * @Assert\NotBlank()
     */
    protected $aw_product_id;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $merchant_product_id;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $merchant_image_url;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $description;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $merchant_category;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $search_price;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $merchant_name;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $merchant_id;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $category_name;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $category_id;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $aw_image_url;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $currency;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $store_price;


    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $delivery_cost;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $merchant_deep_link;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $product_language;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $last_updated;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $display_price;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $data_feed_id;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $brand_name;


    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $brand_id;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $colour;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $product_short_description;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $specifications;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $condition;


    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $product_model;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $model_number;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $dimensions;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $keywords;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $promotional_text;


    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $product_type;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $commission_group;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $merchant_product_category_path;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $merchant_product_second_category;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $merchant_product_third_category;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $rrp_price;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $saving;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $savings_percent;


    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $base_price;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $base_price_amount;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $base_price_text;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $product_price_old;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $delivery_restrictions;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $delivery_weight;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $warranty;


    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $terms_of_contract;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $delivery_time;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $in_stock;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $stock_quantity;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $valid_from;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $valid_to;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $is_for_sale;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $web_offer;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $pre_order;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $stock_status;


    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $size_stock_status;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $size_stock_amount;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $merchant_thumb_url;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $large_image;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $alternate_image;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $aw_thumb_url;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $alternate_image_two;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $alternate_image_three;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $alternate_image_four;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $ean;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $isbn;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $upc;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $mpn;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $parent_product_id;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $product_GTIN;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $basket_link;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $Fashion_suitable_for;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $Fashion_category;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $Fashion_size;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $Fashion_material;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $Fashion_pattern;

    /**
     * @MongoDB\Field(type="string")
     * @Annotation\Type("string")
     */
    protected $Fashion_swatch;

    /**
     * @return mixed
     */
    public function getAwDeepLink()
    {
        return $this->aw_deep_link;
    }

    /**
     * @return mixed
     */
    public function getProductName()
    {
        return $this->product_name;
    }

    /**
     * @return mixed
     */
    public function getAwProductId()
    {
        return $this->aw_product_id;
    }

    /**
     * @return mixed
     */
    public function getMerchantProductId()
    {
        return $this->merchant_product_id;
    }

    /**
     * @return mixed
     */
    public function getMerchantImageUrl()
    {
        return $this->merchant_image_url;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getMerchantCategory()
    {
        return $this->merchant_category;
    }

    /**
     * @return mixed
     */
    public function getSearchPrice()
    {
        return $this->search_price;
    }

    /**
     * @return mixed
     */
    public function getMerchantName()
    {
        return $this->merchant_name;
    }

    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->merchant_id;
    }

    /**
     * @return mixed
     */
    public function getCategoryName()
    {
        return $this->category_name;
    }

    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * @return mixed
     */
    public function getAwImageUrl()
    {
        return $this->aw_image_url;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getStorePrice()
    {
        return $this->store_price;
    }

    /**
     * @return mixed
     */
    public function getDeliveryCost()
    {
        return $this->delivery_cost;
    }

    /**
     * @return mixed
     */
    public function getMerchantDeepLink()
    {
        return $this->merchant_deep_link;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return mixed
     */
    public function getLastUpdated()
    {
        return $this->last_updated;
    }

    /**
     * @return mixed
     */
    public function getDisplayPrice()
    {
        return $this->display_price;
    }

    /**
     * @return mixed
     */
    public function getDataFeedId()
    {
        return $this->data_feed_id;
    }

    /**
     * @return mixed
     */
    public function getBrandName()
    {
        return $this->brand_name;
    }

    /**
     * @return mixed
     */
    public function getBrandId()
    {
        return $this->brand_id;
    }

    /**
     * @return mixed
     */
    public function getColour()
    {
        return $this->colour;
    }

    /**
     * @return mixed
     */
    public function getProductShortDescription()
    {
        return $this->product_short_description;
    }

    /**
     * @return mixed
     */
    public function getSpecifications()
    {
        return $this->specifications;
    }

    /**
     * @return mixed
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @return mixed
     */
    public function getProductModel()
    {
        return $this->product_model;
    }

    /**
     * @return mixed
     */
    public function getModelNumber()
    {
        return $this->model_number;
    }

    /**
     * @return mixed
     */
    public function getDimensions()
    {
        return $this->dimensions;
    }

    /**
     * @return mixed
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @return mixed
     */
    public function getPromotionalText()
    {
        return $this->promotional_text;
    }

    /**
     * @return mixed
     */
    public function getProductType()
    {
        return $this->product_type;
    }

    /**
     * @return mixed
     */
    public function getCommissionGroup()
    {
        return $this->commission_group;
    }

    /**
     * @return mixed
     */
    public function getMerchantProductCategoryPath()
    {
        return $this->merchant_product_category_path;
    }

    /**
     * @return mixed
     */
    public function getMerchantProductSecondCategory()
    {
        return $this->merchant_product_second_category;
    }

    /**
     * @return mixed
     */
    public function getMerchantProductThirdCategory()
    {
        return $this->merchant_product_third_category;
    }

    /**
     * @return mixed
     */
    public function getRrpPrice()
    {
        return $this->rrp_price;
    }

    /**
     * @return mixed
     */
    public function getSaving()
    {
        return $this->saving;
    }

    /**
     * @return mixed
     */
    public function getSavingsPercent()
    {
        return $this->savings_percent;
    }

    /**
     * @return mixed
     */
    public function getBasePrice()
    {
        return $this->base_price;
    }

    /**
     * @return mixed
     */
    public function getBasePriceAmount()
    {
        return $this->base_price_amount;
    }

    /**
     * @return mixed
     */
    public function getBasePriceText()
    {
        return $this->base_price_text;
    }

    /**
     * @return mixed
     */
    public function getProductPriceOld()
    {
        return $this->product_price_old;
    }

    /**
     * @return mixed
     */
    public function getDeliveryRestrictions()
    {
        return $this->delivery_restrictions;
    }

    /**
     * @return mixed
     */
    public function getDeliveryWeight()
    {
        return $this->delivery_weight;
    }

    /**
     * @return mixed
     */
    public function getWarranty()
    {
        return $this->warranty;
    }

    /**
     * @return mixed
     */
    public function getTermsOfContract()
    {
        return $this->terms_of_contract;
    }

    /**
     * @return mixed
     */
    public function getDeliveryTime()
    {
        return $this->delivery_time;
    }

    /**
     * @return mixed
     */
    public function getInStock()
    {
        return $this->in_stock;
    }

    /**
     * @return mixed
     */
    public function getStockQuantity()
    {
        return $this->stock_quantity;
    }

    /**
     * @return mixed
     */
    public function getValidFrom()
    {
        return $this->valid_from;
    }

    /**
     * @return mixed
     */
    public function getValidTo()
    {
        return $this->valid_to;
    }

    /**
     * @return mixed
     */
    public function getIsForSale()
    {
        return $this->is_for_sale;
    }

    /**
     * @return mixed
     */
    public function getWebOffer()
    {
        return $this->web_offer;
    }

    /**
     * @return mixed
     */
    public function getPreOrder()
    {
        return $this->pre_order;
    }

    /**
     * @return mixed
     */
    public function getStockStatus()
    {
        return $this->stock_status;
    }

    /**
     * @return mixed
     */
    public function getSizeStockStatus()
    {
        return $this->size_stock_status;
    }

    /**
     * @return mixed
     */
    public function getSizeStockAmount()
    {
        return $this->size_stock_amount;
    }

    /**
     * @return mixed
     */
    public function getMerchantThumbUrl()
    {
        return $this->merchant_thumb_url;
    }

    /**
     * @return mixed
     */
    public function getLargeImage()
    {
        return $this->large_image;
    }

    /**
     * @return mixed
     */
    public function getAlternateImage()
    {
        return $this->alternate_image;
    }

    /**
     * @return mixed
     */
    public function getAwThumbUrl()
    {
        return $this->aw_thumb_url;
    }

    /**
     * @return mixed
     */
    public function getAlternateImageTwo()
    {
        return $this->alternate_image_two;
    }

    /**
     * @return mixed
     */
    public function getAlternateImageThree()
    {
        return $this->alternate_image_three;
    }

    /**
     * @return mixed
     */
    public function getAlternateImageFour()
    {
        return $this->alternate_image_four;
    }

    /**
     * @return mixed
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * @return mixed
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @return mixed
     */
    public function getUpc()
    {
        return $this->upc;
    }

    /**
     * @return mixed
     */
    public function getMpn()
    {
        return $this->mpn;
    }

    /**
     * @return mixed
     */
    public function getParentProductId()
    {
        return $this->parent_product_id;
    }

    /**
     * @return mixed
     */
    public function getProductGTIN()
    {
        return $this->product_GTIN;
    }

    /**
     * @return mixed
     */
    public function getBasketLink()
    {
        return $this->basket_link;
    }

    /**
     * @return mixed
     */
    public function getFashionSuitableFor()
    {
        return $this->Fashion_suitable_for;
    }

    /**
     * @return mixed
     */
    public function getFashionCategory()
    {
        return $this->Fashion_category;
    }

    /**
     * @return mixed
     */
    public function getFashionSize()
    {
        return $this->Fashion_size;
    }

    /**
     * @return mixed
     */
    public function getFashionMaterial()
    {
        return $this->Fashion_material;
    }

    /**
     * @return mixed
     */
    public function getFashionPattern()
    {
        return $this->Fashion_pattern;
    }

    /**
     * @return mixed
     */
    public function getFashionSwatch()
    {
        return $this->Fashion_swatch;
    }

    public static function getImageColumns(): array
    {
        return [
            'alternate_image_four', 'alternate_image_three', 'alternate_image_three',
            'aw_thumb_url', 'alternate_image', 'aw_image_url',
            'merchant_image_url', 'alternate_image_two', 'large_image'
        ];
    }

    public static function getLinkColumns(): array
    {
        return [
            'aw_deep_link', 'merchant_deep_link'
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
        return array_merge(['aw_product_id', 'brand_name'], parent::getSeparateFilterColumn());
    }

    public static function arrayColumns(): array
    {
        return [];
    }
}