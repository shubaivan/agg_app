<?php

namespace App\QueueModel;

class AdrecordDataRow extends ResourceProductQueues
{
    public function transform()
    {
        $rowData = $this->getRow();
        array_walk($rowData, function ($v, $k) use (&$row) {
            if ($k !== 'shop') {
                return $row[$k] = utf8_encode($v);
            }
            return $row[$k] = $v;
        });

        $row['ImageUrl'] = $row['graphicUrl'];
        $row['TrackingUrl'] = $row['productUrl'];
        $row['originalPrice'] = $row['regularPrice'];
        $row['manufacturerArticleNumber'] = $row['EAN'];
        $row['Extras'] = '';
        if (isset($row['gender']) && strlen($row['gender']) > 0) {
            $row['Extras'] .= '{GENDER#' . $row['gender'] . '}';
        }
        if (isset($row['deliveryTime']) && strlen($row['deliveryTime']) > 0) {
            $row['Extras'] .= '{DELIVERY_TIME#' . $row['deliveryTime'] . '}';
        }
        if (isset($row['inStockQty']) && strlen($row['inStockQty']) > 0) {
            $row['Extras'] .= '{IN_STOCK_QTY#' . $row['inStockQty'] . '}';
        }

        $this->row = $row;
        $this->postTransform();
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
        return $this->row['EAN'] ?? null;
    }
}
