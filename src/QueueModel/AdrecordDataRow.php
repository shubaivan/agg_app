<?php

namespace App\QueueModel;

class AdrecordDataRow implements ResourceDataRow
{
    /**
     * @var array
     */
    private $row;

    /**
     * AdrecordDataRow constructor.
     * @param array $row
     */
    public function __construct(array $row)
    {
        $this->row = $row;
    }

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
        $row['originalPrice'] = $row['regularPrice'];

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
    }

    /**
     * @return array
     */
    public function getRow(): array
    {
        return $this->row;
    }

    /**
     * @return string|null
     */
    public function getShop()
    {
        return $this->row['shop'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getSku()
    {
        return $this->row['SKU'] ?? null;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setExistProductId(int $id)
    {
        if ($this->getRow() && is_array($this->row)) {
            $this->row['id'] = $id;
        }

        return $this;
    }
}
