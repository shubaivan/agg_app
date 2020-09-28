<?php


namespace App\Cache;

use Symfony\Component\Cache\Adapter\AdapterInterface;

class TagAwareQuerySecondLevelCacheCategory extends TagAwareQueryResultCacheParent
{
    /**
     * TagAwareQueryResultCacheCommon constructor.
     */
    public function __construct(AdapterInterface $tagAwareAdapter)
    {
        parent::__construct($tagAwareAdapter);
    }
}