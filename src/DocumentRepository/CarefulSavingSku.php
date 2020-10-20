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
}