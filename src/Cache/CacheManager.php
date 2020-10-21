<?php


namespace App\Cache;

use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Adapter\TraceableAdapter;

class CacheManager
{
    const HTTP_CACHE_MODIFY_PRODUCT_LAST_SHOW_TIME = '_modify_product_last_show';
    const HTTP_CACHE_EXPIRES_TIME = 'http_cache_expires_time';

    /**
     * @var TagAwareAdapter
     */
    private $pdo_common_cache_pool;

    /**
     * @var TagAwareAdapter
     */
    private $pdo_product_cache_pool;

    /**
     * @var TagAwareAdapter
     */
    private $pdo_shop_cache_pool;

    /**
     * @var TagAwareAdapter
     */
    private $pdo_category_cache_pool;

    /**
     * @var TagAwareAdapter
     */
    private $pdo_category_conf_cache_pool;

    /**
     * @var TraceableAdapter
     */
    private $doctrine_result_cache_pool;

    /**
     * @var TraceableAdapter
     */
    private $doctrine_system_cache_pool;

    /**
     * @var TagAwareAdapter
     */
    private $pdo_brand_cache_pool;

    /**
     * @var TagAwareAdapter
     */
    private $pdo_category_second_level_cache_pool;

    /**
     * @var TagAwareAdapter
     */
    private $pdo_brand_second_level_cache_pool;

    /**
     * @var TagAwareAdapter
     */
    private $pdo_shop_second_level_cache_pool;

    /**
     * CacheManager constructor.
     * @param TagAwareAdapter $pdo_common_cache_pool
     * @param TagAwareAdapter $pdo_product_cache_pool
     * @param TagAwareAdapter $pdo_shop_cache_pool
     * @param TagAwareAdapter $pdo_category_cache_pool
     * @param TagAwareAdapter $pdo_category_conf_cache_pool
     * @param $doctrine_result_cache_pool
     * @param $doctrine_system_cache_pool
     * @param TagAwareAdapter $pdo_brand_cache_pool
     * @param TagAwareAdapter $pdo_category_second_level_cache_pool
     * @param TagAwareAdapter $pdo_brand_second_level_cache_pool
     * @param TagAwareAdapter $pdo_shop_second_level_cache_pool
     */
    public function __construct(
        TagAwareAdapter $pdo_common_cache_pool,
        TagAwareAdapter $pdo_product_cache_pool,
        TagAwareAdapter $pdo_shop_cache_pool,
        TagAwareAdapter $pdo_category_cache_pool,
        TagAwareAdapter $pdo_category_conf_cache_pool,
        $doctrine_result_cache_pool,
        $doctrine_system_cache_pool,
        TagAwareAdapter $pdo_brand_cache_pool,
        TagAwareAdapter $pdo_category_second_level_cache_pool,
        TagAwareAdapter $pdo_brand_second_level_cache_pool,
        TagAwareAdapter $pdo_shop_second_level_cache_pool
    )
    {
        $this->pdo_common_cache_pool = $pdo_common_cache_pool;
        $this->pdo_product_cache_pool = $pdo_product_cache_pool;
        $this->pdo_shop_cache_pool = $pdo_shop_cache_pool;
        $this->pdo_category_cache_pool = $pdo_category_cache_pool;
        $this->pdo_category_conf_cache_pool = $pdo_category_conf_cache_pool;
        $this->doctrine_result_cache_pool = $doctrine_result_cache_pool;
        $this->doctrine_system_cache_pool = $doctrine_system_cache_pool;
        $this->pdo_brand_cache_pool = $pdo_brand_cache_pool;
        $this->pdo_category_second_level_cache_pool = $pdo_category_second_level_cache_pool;
        $this->pdo_brand_second_level_cache_pool = $pdo_brand_second_level_cache_pool;
        $this->pdo_shop_second_level_cache_pool = $pdo_shop_second_level_cache_pool;
    }


    public function clearAllPoolsCache()
    {
        $this->pdo_common_cache_pool->clear();
        $this->pdo_product_cache_pool->clear();
        $this->pdo_shop_cache_pool->clear();
        $this->pdo_category_cache_pool->clear();
        $this->pdo_category_conf_cache_pool->clear();
        $this->doctrine_result_cache_pool->clear();
        $this->doctrine_system_cache_pool->clear();
        $this->pdo_brand_cache_pool->clear();
        $this->pdo_category_second_level_cache_pool->clear();
        $this->pdo_brand_second_level_cache_pool->clear();
        $this->pdo_shop_second_level_cache_pool->clear();
    }
}