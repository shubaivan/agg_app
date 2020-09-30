<?php


namespace App\QueueModel;

use App\Document\AwinProduct;

class AwinDataRow extends ResourceProductQueues
{
    protected static $mongoClass = AwinProduct::class;
    
    public function transform()
    {
        $rowData = $this->getRow();

        $rowData['product_language'] = $rowData['language'];
        $rowData['trackingUrl'] = $rowData['aw_deep_link'];
        $rowData['name'] = $rowData['product_name'];
        $rowData['sku'] = $rowData['aw_product_id'];
        $rowData['imageUrl'] = $rowData['merchant_image_url'];

        if (isset($rowData['merchant_category']) && $rowData['merchant_category']) {
            $merchant_category = $rowData['merchant_category'];
            $expMerchant_category = preg_split('/>|:/', $merchant_category);
            if (count($expMerchant_category)) {
                $filter = array_filter($expMerchant_category, function ($v) {
                    return strlen($v);
                });
                $map = array_map(function ($v) {
                    return trim($v);
                }, $filter);
                $this->categories = array_merge($this->categories, $map);
            }
        }
        
        $rowData['price'] = $rowData['search_price'];
        $rowData['manufacturerArticleNumber'] = $rowData['mpn'];

        if (isset($rowData['category_name']) && $rowData['category_name']) {
            $this->categories[] = $rowData['category_name'];
        }

        $rowData['Extras'] = '';

        if (isset($rowData['aw_image_url']) && strlen($rowData['aw_image_url']) > 0) {
            $rowData['Extras'] .= '{ALTERNATIVE_IMAGE_1#' . $rowData['aw_image_url'] . '}';
        }

        $rowData['productUrl'] = $rowData['merchant_deep_link'];

        $rowData['brand'] = $rowData['brand_name'];
        if (isset($rowData['colour']) && strlen($rowData['colour']) > 0) {
            $rowData['Extras'] .= '{COLOUR#' . $rowData['colour'] . '}';
        }

        $rowData['productShortDescription'] = $rowData['product_short_description'];

        $explodeMerchantProductCategoryPath = preg_split('/>|:/', $rowData['merchant_product_category_path']);
        if (count($explodeMerchantProductCategoryPath)) {
            $explodeMerchantProductCategoryPathFilter = array_filter($explodeMerchantProductCategoryPath, function ($v) {
                return strlen($v);
            });
            $explodeMerchantProductCategoryPathMap = array_map(function ($v) {
                return trim($v);
            }, $explodeMerchantProductCategoryPathFilter);
            $this->categories = array_merge($this->categories, $explodeMerchantProductCategoryPathMap);
        }
        

        if (isset($rowData['dimensions']) && strlen($rowData['dimensions']) > 0) {
            $rowData['Extras'] .= '{SIZE#' . $rowData['dimensions'] . '}';
        }

        if (isset($rowData['Fashion:size'])) {
            $rowData['Fashion_size'] = $rowData['Fashion:size'];
            unset($rowData['Fashion:size']);
            if (strlen($rowData['Fashion_size']) > 0) {
                $rowData['Extras'] .= '{SIZE#' . $rowData['Fashion_size'] . '}';
            }
        } else {
            $rowData['Fashion_size'] = '';
        }

        $rowData['deliveryRestrictions'] = $rowData['delivery_restrictions'];
        $rowData['instock'] = $rowData['in_stock'];

        if (isset($rowData['alternate_image']) && strlen($rowData['alternate_image']) > 0) {
            $rowData['Extras'] .= '{ALTERNATIVE_IMAGE#' . $rowData['alternate_image'] . '}';
        }

        if (isset($rowData['alternate_image']) && strlen($rowData['alternate_image']) > 0) {
            $rowData['Extras'] .= '{ALTERNATIVE_IMAGE_2#' . $rowData['alternate_image'] . '}';
        }

        if (isset($rowData['aw_thumb_url']) && strlen($rowData['aw_thumb_url']) > 0) {
            $rowData['Extras'] .= '{ALTERNATIVE_IMAGE_3#' . $rowData['aw_thumb_url'] . '}';
        }

        if (isset($rowData['alternate_image_two']) && strlen($rowData['alternate_image_two']) > 0) {
            $rowData['Extras'] .= '{ALTERNATIVE_IMAGE_4#' . $rowData['alternate_image_two'] . '}';
        }

        if (isset($rowData['alternate_image_three']) && strlen($rowData['alternate_image_three']) > 0) {
            $rowData['Extras'] .= '{ALTERNATIVE_IMAGE_5#' . $rowData['alternate_image_three'] . '}';
        }

        if (isset($rowData['alternate_image_four']) && strlen($rowData['alternate_image_four']) > 0) {
            $rowData['Extras'] .= '{ALTERNATIVE_IMAGE_6#' . $rowData['alternate_image_four'] . '}';
        }
        
        if (isset($rowData['alternate_image_four']) && strlen($rowData['alternate_image_four']) > 0) {
            $rowData['Extras'] .= '{ALTERNATIVE_IMAGE_6#' . $rowData['alternate_image_four'] . '}';
        }

        if (isset($rowData['large_image']) && strlen($rowData['large_image']) > 0) {
            $rowData['Extras'] .= '{ALTERNATIVE_IMAGE_7#' . $rowData['large_image'] . '}';
        }

        if (isset($rowData['Fashion:suitable_for'])) {
            $rowData['Fashion_suitable_for'] = $rowData['Fashion:suitable_for'];
            unset($rowData['Fashion:suitable_for']);
        } else {
            $rowData['Fashion_suitable_for'] = '';
        }

        if (isset($rowData['Fashion:category'])) {
            $rowData['Fashion_category'] = $rowData['Fashion:category'];
            unset($rowData['Fashion:category']);
            if (strlen($rowData['Fashion_category'])) {
                $this->categories[] = $rowData['Fashion_category'];   
            }
        } else {
            $rowData['Fashion_category'] = '';
        }

        if (isset($rowData['Fashion:material'])) {
            $rowData['Fashion_material'] = $rowData['Fashion:material'];
            unset($rowData['Fashion:material']);
            if (strlen($rowData['Fashion_material'])) {
                $rowData['Extras'] .= '{MATERIAL#' . $rowData['Fashion_material'] . '}';
            }
        } else {
            $rowData['Fashion_material'] = '';
        }

        if (isset($rowData['delivery_time']) && strlen($rowData['delivery_time'])) {
            $rowData['Extras'] .= '{DELIVERY_TIME#' . $rowData['delivery_time'] . '}';
        }

        if (isset($rowData['condition']) && strlen($rowData['condition'])) {
            $rowData['Extras'] .= '{CONDITION#' . $rowData['condition'] . '}';
        }

        if (isset($rowData['Fashion:pattern'])) {
            $rowData['Fashion_pattern'] = $rowData['Fashion:pattern'];
            unset($rowData['Fashion:pattern']);
            if (strlen($rowData['Fashion_pattern'])) {
                $rowData['Extras'] .= '{PATTERN#' . $rowData['Fashion_pattern'] . '}';
            }
        } else {
            $rowData['Fashion_pattern'] = '';
        }

        if (isset($rowData['Fashion:swatch'])) {
            $rowData['Fashion_swatch'] = $rowData['Fashion:swatch'];
            unset($rowData['Fashion:swatch']);
        } else {
            $rowData['Fashion_swatch'] = '';
        }
        $this->row = $rowData;
        $this->postTransform();
    }

    public function getName()
    {
        return $this->row['product_name'] ?? null;
    }
    
    public function getSku()
    {
        return $this->row['aw_product_id'] ?? null;
    }

    public function getBrand()
    {
        return $this->row['brand_name'] ?? null;    
    }

    public function getEan()
    {
        return $this->row['ean'] ?? null;
    }
}