<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
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
    protected $language;

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
}