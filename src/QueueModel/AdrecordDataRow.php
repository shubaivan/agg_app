<?php

namespace App\QueueModel;

class AdrecordDataRow extends Queues implements ResourceDataRow, LastProductInterface
{
    /**
     * @var array
     */
    private $row;

    /**
     * @var bool
     */
    private $lastProduct;

    /**
     * @var string
     */
    private $filePath;

    /**
     * AdrecordDataRow constructor.
     * @param array $row
     * @param string $filePath
     * @param string $redisUniqKey
     * @param bool $lastProduct
     */
    public function __construct(
        array $row,
        string $filePath,
        string $redisUniqKey,
        bool $lastProduct = false
    ) {
        $this->row = $row;
        $this->lastProduct = $lastProduct;
        $this->redisUniqKey = $redisUniqKey;
        $this->filePath = $filePath;
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
}
