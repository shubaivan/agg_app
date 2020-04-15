<?php


namespace App\Cache;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Cache\DoctrineProvider;

class TagAwareQueryResultCacheFactory extends DoctrineProvider
{
    /**
     * @var TagAwareAdapterInterface
     */
    private $tagAwareAdapter;

    /**
     * @var array
     */
    private $queryTags = [];

    /**
     * @var string
     */
    private $currentIdWithoutNamespace;

    /**
     * @var QueryCacheProfile
     */
    private $queryCacheProfile;

    /**
     * @var string
     */
    private $query;

    /**
     * @var array
     */
    private $params;

    /**
     * @var array
     */
    private $types;

    /**
     * @param TagAwareAdapterInterface $tagAwareAdapter
     */
    public function __construct(AdapterInterface $tagAwareAdapter)
    {
        parent::__construct($tagAwareAdapter);
        $this->tagAwareAdapter = $tagAwareAdapter;
    }

    /**
     * @param $query
     * @param array $params
     * @param array $types
     *
     * @return string
     */
    private function getDoctrineQueryCacheKey($query, array $params, array $types)
    {
//        return (new QueryCacheProfile())->generateCacheKeys($query, $params, $types)[0];
        return $this->getQueryCacheProfile()->generateCacheKeys($query, $params, $types)[0];
    }

    /**
     * @return QueryCacheProfile
     */
    public function getQueryCacheProfile()
    {
        return $this->queryCacheProfile;
    }

    /**
     * @param int $lifetime
     * @param string $cacheKey
     * @return $this
     */
    public function setQueryCacheProfile(int $lifetime = 0, string $cacheKey = '')
    {
        $this->queryCacheProfile = new QueryCacheProfile($lifetime, $cacheKey, $this);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        $this->currentIdWithoutNamespace = $id;

        return parent::save($id, $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        /** @var CacheItem $item */
        $item = $this->tagAwareAdapter->getItem(rawurlencode($id));

        if (isset($this->queryTags[$this->currentIdWithoutNamespace])) {
            $item->tag($this->queryTags[$this->currentIdWithoutNamespace]);
        }

        if (0 < $lifeTime) {
            $item->expiresAfter($lifeTime);
        }

        $this->currentIdWithoutNamespace = null;

        return $this->tagAwareAdapter->save($item->set($data));
    }

    /**
     * @param $query
     * @param array $params
     * @param array $types
     * @param array $tags
     * @param int $lifetime
     * @param string $cacheKey
     * @return $this
     */
    public function setQueryCacheTags(
        $query,
        array $params,
        array $types,
        array $tags,
        int $lifetime = 0,
        string $cacheKey = '')
    {
        $this->setQueryCacheProfile($lifetime, $cacheKey);
        $this->query = $query;
        $this->params = $params;
        $this->types = $types;
        $this->queryTags[$this->getDoctrineQueryCacheKey($query, $params, $types)] = $tags;

        return $this;
    }

    /**
     * @return array
     */
    public function prepareParamsForExecuteCacheQuery()
    {
        return [$this->query, $this->params, $this->types, $this->getQueryCacheProfile()];
    }

    /**
     * @return TagAwareAdapterInterface
     */
    public function getTagAwareAdapter()
    {
        return $this->tagAwareAdapter;
    }

    public function __invoke($t)
    {
        $this->tagAwareAdapter = $t;
    }
}