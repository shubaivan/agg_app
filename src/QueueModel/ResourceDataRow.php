<?php

namespace App\QueueModel;

interface ResourceDataRow extends LastProductInterface
{
    /**
     * @return array
     */
    public function getRow(): array;

    /**
     * @return string|null
     */
    public function getShop();

    /**
     * @return string|null
     */
    public function getSku();

    /**
     * @param string $sku
     * @return mixed
     */
    public function setSkuValueToRow(string $sku);

    /**
     * @param int $id
     * @return $this
     */
    public function setExistProductId(int $id);

    /*
     * 
     */
    public function unsetId();
    
    /**
     * @return string
     */
    public function getFilePath();

    /**
     * @return string
     */
    public function getRedisUniqKey(): string;

    /**
     * @return mixed
     */
    public function generateIdentityUniqData();
}
