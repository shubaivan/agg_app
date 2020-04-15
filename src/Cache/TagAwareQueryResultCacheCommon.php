<?php


namespace App\Cache;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Cache\DoctrineProvider;

class TagAwareQueryResultCacheCommon extends TagAwareQueryResultCacheFactory
{
    /**
     * TagAwareQueryResultCacheCommon constructor.
     */
    public function __construct(AdapterInterface $tagAwareAdapter)
    {
        parent::__construct($tagAwareAdapter);
    }
}