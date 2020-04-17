<?php


namespace App\Cache;

use Symfony\Component\Cache\Adapter\AdapterInterface;

class TagAwareQueryResultCacheShop extends TagAwareQueryResultCacheParent
{
    /**
     * TagAwareQueryResultCacheCommon constructor.
     */
    public function __construct(AdapterInterface $tagAwareAdapter)
    {
        parent::__construct($tagAwareAdapter);
    }
}