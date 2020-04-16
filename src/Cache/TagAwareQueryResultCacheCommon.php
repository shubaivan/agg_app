<?php


namespace App\Cache;

use Symfony\Component\Cache\Adapter\AdapterInterface;

class TagAwareQueryResultCacheCommon extends TagAwareQueryResultCacheParent
{
    /**
     * TagAwareQueryResultCacheCommon constructor.
     */
    public function __construct(AdapterInterface $tagAwareAdapter)
    {
        parent::__construct($tagAwareAdapter);
    }
}