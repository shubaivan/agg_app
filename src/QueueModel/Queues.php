<?php


namespace App\QueueModel;


abstract class Queues
{
    /**
     * @var string
     */
    protected $redisUniqKey;

    /**
     * @return string
     */
    public function getRedisUniqKey(): string
    {
        return $this->redisUniqKey;
    }
}