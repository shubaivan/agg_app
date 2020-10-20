<?php


namespace App\Cache;

use Symfony\Component\Cache\Adapter\AdapterInterface;

class TagAwareQuerySecondLevelCacheShop extends TagAwareQueryResultCacheParent
{
    /**
     * TagAwareQueryResultCacheCommon constructor.
     */
    public function __construct(AdapterInterface $tagAwareAdapter)
    {
        parent::__construct($tagAwareAdapter);
    }
}