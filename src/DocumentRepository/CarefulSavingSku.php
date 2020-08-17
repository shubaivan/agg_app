<?php


namespace App\DocumentRepository;


use App\QueueModel\ResourceProductQueues;

interface CarefulSavingSku
{
    /**
     * @param string $sku
     * @return mixed
     */
    public function matchExistProduct(string $sku);

    /**
     * @param ResourceProductQueues $productQueues
     * @param string $shop
     * @return mixed
     */
    public function createProduct(ResourceProductQueues $productQueues, string $shop);
}