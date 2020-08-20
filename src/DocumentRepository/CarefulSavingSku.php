<?php


namespace App\DocumentRepository;


use App\QueueModel\ResourceProductQueues;

interface CarefulSavingSku
{
    /**
     * @param ResourceProductQueues $productQueues
     * @return mixed
     */
    public function matchExistProduct(ResourceProductQueues $productQueues);

    /**
     * @param ResourceProductQueues $productQueues
     * @param string $shop
     * @return mixed
     */
    public function createProduct(ResourceProductQueues $productQueues, string $shop);
}