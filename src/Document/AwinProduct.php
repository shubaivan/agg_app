<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use App\DocumentRepository\AwinProductRepository;
use JMS\Serializer\Annotation;

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
 *     "shop"="text"
 * })
 * @Annotation\AccessorOrder("custom", custom = {
 *     "aw_product_id",
 *     "merchant_image_url",
 *     "decline",
 *     "declineReasonClass",
 *     "shop",
 *     "aw_deep_link",
 *     "product_name",
 *     "search_price"
 * })
 */
class AwinProduct extends AbstractDocument
{
    /**
     * @MongoDB\Field(type="string")
     */
    protected $aw_deep_link;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $product_name;
    
    /**
     * @MongoDB\Field(type="string")
     * @MongoDB\UniqueIndex(order="asc")
     */
    protected $aw_product_id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $merchant_product_id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $merchant_image_url;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $description;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $merchant_category;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $search_price;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $merchant_name;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $merchant_id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $category_name;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $category_id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $aw_image_url;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $currency;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $store_price;


    /**
     * @MongoDB\Field(type="string")
     */
    protected $delivery_cost;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $merchant_deep_link;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $product_language;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $last_updated;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $display_price;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $data_feed_id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $brand_name;


    /**
     * @MongoDB\Field(type="string")
     */
    protected $brand_id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $colour;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $product_short_description;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $specifications;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $condition;


    /**
     * @MongoDB\Field(type="string")
     */
    protected $product_model;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $model_number;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $dimensions;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $keywords;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $promotional_text;


    /**
     * @MongoDB\Field(type="string")
     */
    protected $product_type;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $commission_group;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $merchant_product_category_path;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $merchant_product_second_category;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $merchant_product_third_category;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $rrp_price;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $saving;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $savings_percent;


    /**
     * @MongoDB\Field(type="string")
     */
    protected $base_price;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $base_price_amount;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $base_price_text;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $product_price_old;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $delivery_restrictions;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $delivery_weight;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $warranty;


    /**
     * @MongoDB\Field(type="string")
     */
    protected $terms_of_contract;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $delivery_time;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $in_stock;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $stock_quantity;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $valid_from;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $valid_to;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $is_for_sale;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $web_offer;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $pre_order;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $stock_status;


    /**
     * @MongoDB\Field(type="string")
     */
    protected $size_stock_status;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $size_stock_amount;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $merchant_thumb_url;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $large_image;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $alternate_image;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $aw_thumb_url;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $alternate_image_two;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $alternate_image_three;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $alternate_image_four;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $ean;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $isbn;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $upc;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $mpn;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $parent_product_id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $product_GTIN;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $basket_link;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $Fashion_suitable_for;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $Fashion_category;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $Fashion_size;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $Fashion_material;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $Fashion_pattern;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $Fashion_swatch;

    /**
     * AwinProduct constructor.
     * @param $aw_deep_link
     * @param $product_name
     * @param $aw_product_id
     * @param $merchant_product_id
     * @param $merchant_image_url
     * @param $description
     * @param $merchant_category
     * @param $search_price
     * @param $merchant_name
     * @param $merchant_id
     * @param $category_name
     * @param $category_id
     * @param $aw_image_url
     * @param $currency
     * @param $store_price
     * @param $delivery_cost
     * @param $merchant_deep_link
     * @param $language
     * @param $last_updated
     * @param $display_price
     * @param $data_feed_id
     * @param $brand_name
     * @param $brand_id
     * @param $colour
     * @param $product_short_description
     * @param $specifications
     * @param $condition
     * @param $product_model
     * @param $model_number
     * @param $dimensions
     * @param $keywords
     * @param $promotional_text
     * @param $product_type
     * @param $commission_group
     * @param $merchant_product_category_path
     * @param $merchant_product_second_category
     * @param $merchant_product_third_category
     * @param $rrp_price
     * @param $saving
     * @param $savings_percent
     * @param $base_price
     * @param $base_price_amount
     * @param $base_price_text
     * @param $product_price_old
     * @param $delivery_restrictions
     * @param $delivery_weight
     * @param $warranty
     * @param $terms_of_contract
     * @param $delivery_time
     * @param $in_stock
     * @param $stock_quantity
     * @param $valid_from
     * @param $valid_to
     * @param $is_for_sale
     * @param $web_offer
     * @param $pre_order
     * @param $stock_status
     * @param $size_stock_status
     * @param $size_stock_amount
     * @param $merchant_thumb_url
     * @param $large_image
     * @param $alternate_image
     * @param $aw_thumb_url
     * @param $alternate_image_two
     * @param $alternate_image_three
     * @param $alternate_image_four
     * @param $ean
     * @param $isbn
     * @param $upc
     * @param $mpn
     * @param $parent_product_id
     * @param $product_GTIN
     * @param $basket_link
     * @param $Fashion_suitable_for
     * @param $Fashion_category
     * @param $Fashion_size
     * @param $Fashion_material
     * @param $Fashion_pattern
     * @param $Fashion_swatch
     */
    public function __construct($aw_deep_link, $product_name, $aw_product_id, $merchant_product_id, $merchant_image_url, $description, $merchant_category, $search_price, $merchant_name, $merchant_id, $category_name, $category_id, $aw_image_url, $currency, $store_price, $delivery_cost, $merchant_deep_link, $language, $last_updated, $display_price, $data_feed_id, $brand_name, $brand_id, $colour, $product_short_description, $specifications, $condition, $product_model, $model_number, $dimensions, $keywords, $promotional_text, $product_type, $commission_group, $merchant_product_category_path, $merchant_product_second_category, $merchant_product_third_category, $rrp_price, $saving, $savings_percent, $base_price, $base_price_amount, $base_price_text, $product_price_old, $delivery_restrictions, $delivery_weight, $warranty, $terms_of_contract, $delivery_time, $in_stock, $stock_quantity, $valid_from, $valid_to, $is_for_sale, $web_offer, $pre_order, $stock_status, $size_stock_status, $size_stock_amount, $merchant_thumb_url, $large_image, $alternate_image, $aw_thumb_url, $alternate_image_two, $alternate_image_three, $alternate_image_four, $ean, $isbn, $upc, $mpn, $parent_product_id, $product_GTIN, $basket_link, $Fashion_suitable_for, $Fashion_category, $Fashion_size, $Fashion_material, $Fashion_pattern, $Fashion_swatch, $shop)
    {
        $this->aw_deep_link = $aw_deep_link;
        $this->product_name = $product_name;
        $this->aw_product_id = $aw_product_id;
        $this->merchant_product_id = $merchant_product_id;
        $this->merchant_image_url = $merchant_image_url;
        $this->description = $description;
        $this->merchant_category = $merchant_category;
        $this->search_price = $search_price;
        $this->merchant_name = $merchant_name;
        $this->merchant_id = $merchant_id;
        $this->category_name = $category_name;
        $this->category_id = $category_id;
        $this->aw_image_url = $aw_image_url;
        $this->currency = $currency;
        $this->store_price = $store_price;
        $this->delivery_cost = $delivery_cost;
        $this->merchant_deep_link = $merchant_deep_link;
        $this->language = $language;
        $this->last_updated = $last_updated;
        $this->display_price = $display_price;
        $this->data_feed_id = $data_feed_id;
        $this->brand_name = $brand_name;
        $this->brand_id = $brand_id;
        $this->colour = $colour;
        $this->product_short_description = $product_short_description;
        $this->specifications = $specifications;
        $this->condition = $condition;
        $this->product_model = $product_model;
        $this->model_number = $model_number;
        $this->dimensions = $dimensions;
        $this->keywords = $keywords;
        $this->promotional_text = $promotional_text;
        $this->product_type = $product_type;
        $this->commission_group = $commission_group;
        $this->merchant_product_category_path = $merchant_product_category_path;
        $this->merchant_product_second_category = $merchant_product_second_category;
        $this->merchant_product_third_category = $merchant_product_third_category;
        $this->rrp_price = $rrp_price;
        $this->saving = $saving;
        $this->savings_percent = $savings_percent;
        $this->base_price = $base_price;
        $this->base_price_amount = $base_price_amount;
        $this->base_price_text = $base_price_text;
        $this->product_price_old = $product_price_old;
        $this->delivery_restrictions = $delivery_restrictions;
        $this->delivery_weight = $delivery_weight;
        $this->warranty = $warranty;
        $this->terms_of_contract = $terms_of_contract;
        $this->delivery_time = $delivery_time;
        $this->in_stock = $in_stock;
        $this->stock_quantity = $stock_quantity;
        $this->valid_from = $valid_from;
        $this->valid_to = $valid_to;
        $this->is_for_sale = $is_for_sale;
        $this->web_offer = $web_offer;
        $this->pre_order = $pre_order;
        $this->stock_status = $stock_status;
        $this->size_stock_status = $size_stock_status;
        $this->size_stock_amount = $size_stock_amount;
        $this->merchant_thumb_url = $merchant_thumb_url;
        $this->large_image = $large_image;
        $this->alternate_image = $alternate_image;
        $this->aw_thumb_url = $aw_thumb_url;
        $this->alternate_image_two = $alternate_image_two;
        $this->alternate_image_three = $alternate_image_three;
        $this->alternate_image_four = $alternate_image_four;
        $this->ean = $ean;
        $this->isbn = $isbn;
        $this->upc = $upc;
        $this->mpn = $mpn;
        $this->parent_product_id = $parent_product_id;
        $this->product_GTIN = $product_GTIN;
        $this->basket_link = $basket_link;
        $this->Fashion_suitable_for = $Fashion_suitable_for;
        $this->Fashion_category = $Fashion_category;
        $this->Fashion_size = $Fashion_size;
        $this->Fashion_material = $Fashion_material;
        $this->Fashion_pattern = $Fashion_pattern;
        $this->Fashion_swatch = $Fashion_swatch;
        $this->shop = $shop;
    }

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

    public static function getImageColumns():array 
    {
        return [
            'alternate_image_four', 'alternate_image_three', 'alternate_image_three',
            'aw_thumb_url', 'alternate_image', 'aw_image_url',
            'merchant_image_url', 'alternate_image_two', 'large_image'
        ];
    }

    public static function getLinkColumns():array
    {
        return [
            'aw_deep_link', 'merchant_deep_link'
        ];
    }
    
    public static function getShortPreviewText():array
    {
        return [
            'description', 'id', 'SKU'
        ];
    }

    public static function getSeparateFilterColumn(): array
    {
        return array_merge(['aw_product_id'], parent::getSeparateFilterColumn());
    }
}