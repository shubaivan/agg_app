<?php

namespace App\QueueModel;

use App\Document\TradeDoublerProduct;
use App\Entity\Product;

class TradeDoublerDataRow extends ResourceProductQueues implements ResourceDataRow
{
    protected static $mongoClass = TradeDoublerProduct::class;
    
    public function transform()
    {
        $imgCounter = 1;
        $rowData = $this->getRow();

        if (isset($rowData['id'])) {
            $rowData['tradeDoublerId'] = $rowData['id'];
            unset($rowData['id']);
        }
        
        $rowData['productLanguage'] = $rowData['language'];
        $rowData['trackingUrl'] = $rowData['productUrl'];
        $rowData['productShortDescription'] = $rowData['shortDescription'];
        $rowData['productModel'] = $rowData['model'];
        $rowData['instock'] = $rowData['inStock'];


        $explodeMerchantProductCategoryPath = explode('>', $rowData['categories']);
        if (count($explodeMerchantProductCategoryPath)) {
            $explodeMerchantProductCategoryPathFilter = array_filter($explodeMerchantProductCategoryPath, function ($v) {
                return strlen($v);
            });
            $explodeMerchantProductCategoryPathMap = array_map(function ($v) {
                return preg_replace('/::/', '', trim($v));
            }, $explodeMerchantProductCategoryPathFilter);
            $this->categories = array_merge($this->categories, $explodeMerchantProductCategoryPathMap);
        }

        $explodeMerchantProductCategoryPath = explode('>', $rowData['MerchantCategoryName']);
        if (count($explodeMerchantProductCategoryPath)) {
            $explodeMerchantProductCategoryPathFilter = array_filter($explodeMerchantProductCategoryPath, function ($v) {
                return strlen($v);
            });
            $explodeMerchantProductCategoryPathMap = array_map(function ($v) {
                return trim($v);
            }, $explodeMerchantProductCategoryPathFilter);
            $this->categories = array_merge($this->categories, $explodeMerchantProductCategoryPathMap);
        }

        $clearImagePath = preg_replace('/;;/', '', $rowData['productImage']);
        $rowData['productImage'] = $clearImagePath;

        $result = filter_var($rowData['shortDescription'], FILTER_VALIDATE_URL);
        if ($result) {
            $rowData['productImage'] = $rowData['shortDescription'];
            $rowData['Extras'] .= '{ALTERNATIVE_IMAGE_'.$imgCounter.'#' . $clearImagePath . '}';
            $imgCounter++;
        }

        if ($rowData['imageUrl'] != $clearImagePath) {
            $rowData['Extras'] .= '{ALTERNATIVE_IMAGE_'.$imgCounter.'#' . $clearImagePath . '}';
            $imgCounter++;
        }

        if (isset($rowData['TDCategoryName']) && strlen($rowData['TDCategoryName'])) {
            $this->categories[] = $rowData['TDCategoryName'];
        }
        $rowData['manufacturerArticleNumber'] = $rowData['groupingId'];

        $rowData['Extras'] = '';
        if (isset($rowData['size']) && strlen($rowData['size']) > 0) {
            $rowData['Extras'] .= '{SIZE#' . $rowData['size'] . '}';
        }

        if (isset($rowData['deliveryTime']) && strlen($rowData['deliveryTime']) > 0) {
            $rowData['Extras'] .= '{DELIVERY_TIME#' . $rowData['deliveryTime'] . '}';
        }

        if (isset($rowData['condition']) && strlen($rowData['condition']) > 0) {
            $rowData['Extras'] .= '{CONDITION#' . $rowData['condition'] . '}';
        }

        if (isset($rowData['height']) && strlen($rowData['height']) > 0) {
            $rowData['Extras'] .= '{HEIGHT#' . $rowData['height'] . '}';
        }

        if (isset($rowData['width']) && strlen($rowData['width']) > 0) {
            $rowData['Extras'] .= '{WIDTH#' . $rowData['width'] . '}';
        }

        $explodeFields = explode(';', $rowData['fields']);

        foreach ($explodeFields as $field) {
            if (preg_match('/additional_image_url:/', $field)) {
                $fieldAdditionalImageUrl = preg_replace('/additional_image_url:/', '', $field);
                $imgs = explode(',', $fieldAdditionalImageUrl);
                $i = 1;
                foreach ($imgs as $key => $img) {
                    $i += 1;
//                    $rowData['Extras'] .= '{ALTERNATIVE_IMAGE_' . $i . '#' . $img . '}';
                    $rowData['Extras'] .= '{ALTERNATIVE_IMAGE_'.$imgCounter.'#' . $img . '}';
                    $imgCounter++;
                }
            } else {
                $explodeFields = explode(':', $field);
                if (isset($explodeFields[0]) && isset($explodeFields[1])) {
                    if (preg_match('/color/', $explodeFields[0])) {
                        $rowData['Extras'] .= '{' . Product::COLOUR . '#' . $explodeFields[1] . '}';
                        continue;
                    }

                    $rowData['Extras'] .= '{' . mb_strtoupper($explodeFields[0]) . '#' . $explodeFields[1] . '}';
                }
            }
        }
//        if (isset($rowData['sku']) && !strlen($rowData['sku'])) {
//            if (isset($rowData['tradeDoublerId'])) {
//                $rowData['sku'] = $rowData['tradeDoublerId'];
//            } elseif (isset($rowData['TDProductId'])) {
//                $rowData['sku'] = $rowData['TDProductId'];
//            }
//
//        }
        $this->row = $rowData;
        $this->postTransform();
    }

    public function getSku()
    {
        return $this->row['sku'] ?? null;
    }

    public function getName()
    {
        return $this->row['name'] ?? null;
    }

    public function getBrand()
    {
        return $this->row['brand'] ?? null;
    }

    public function getEan()
    {
        return $this->row['ean'] ?? null;
    }

    /**
     * @return false|mixed|string|string[]|null
     */
    public function generateIdentityUniqData()
    {
        if (isset($this->row['identityUniqData']) && strlen($this->row['identityUniqData'])) {
            return $this->row['identityUniqData'];
        }

        $prepare = [];

        if ($this->getAttributeByName('identifiers') && strlen($this->getAttributeByName('identifiers'))) {
            $prepare[] = $this->getAttributeByName('identifiers');
        }

        if ($this->getAttributeByName('tradeDoublerId') && strlen($this->getAttributeByName('tradeDoublerId'))) {
            $prepare[] = $this->getAttributeByName('tradeDoublerId');
        }

        $implode = implode('_', $prepare);

        $preg_replace = preg_replace('/[\s+,.]+/', '_', $implode);

        return mb_strtolower($preg_replace);
    }
}
