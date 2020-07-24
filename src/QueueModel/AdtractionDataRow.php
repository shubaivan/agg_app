<?php

namespace App\QueueModel;

class AdtractionDataRow extends Queues implements ResourceDataRow, LastProductInterface
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
     * AdtractionDataRow constructor.
     * @param array $row
     * @param string $filePath
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
}
