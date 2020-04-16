<?php

namespace App\Controller;

use App\Cache\CacheManager;
use App\Entity\Shop;
use App\QueueModel\FileReadyDownloaded;
use App\Services\ObjectsHandler;
use App\Util\RedisHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class StatisticController extends AbstractController
{
    /**
     * @var ObjectsHandler
     */
    private $handler;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var RedisHelper
     */
    private $redisHelper;

    /**
     * FileDownloadController constructor.
     * @param ObjectsHandler $handler
     * @param CacheManager $cacheManager
     * @param RedisHelper $redisHelper
     */
    public function __construct(
        ObjectsHandler $handler,
        CacheManager $cacheManager,
        RedisHelper $redisHelper)
    {
        $this->handler = $handler;
        $this->cacheManager = $cacheManager;
        $this->redisHelper = $redisHelper;
    }


    /**
     * @Route(name="clear_pools_cache", path="cache/clear/pools")
     */
    public function clearAllPoolsCacheAction()
    {
        $this->getCacheManager()->clearAllPoolsCache();
        $articleContent = <<<EOF
**successful** all keay was removed.
EOF;
        return new Response('<html><body>' . $articleContent . '</body></html>');
    }

    /**
     * @Route(name="statistics", path="statistics")
     */
    public function bufferTestAction()
    {
        $prefixes = Shop::getPrefixes();
        $statisticByShops = [];
        foreach ($prefixes as $blockName => $block) {
            if (is_array($block)) {
                foreach ($block as $prefixName => $prefix) {
                    $shopData = $this->getRedisHelper()->keys($prefix . '*');
                    if (is_array($shopData)) {
                        foreach ($shopData as $data) {
                            if (preg_match('/([^:]*)$/', $data, $matches) > 0) {
                                $shopName = array_shift($matches);
                                $statisticByShops[ucfirst($blockName)][$prefixName][$shopName] = $this->getRedisHelper()
                                    ->get($data);
                            }
                        }
                    }
                }

            }
        }

        return $this->render('statistics/statistics.html.twig', [
            'data' => $statisticByShops
        ]);
    }


    /**
     * @return ObjectsHandler
     */
    public function getHandler(): ObjectsHandler
    {
        return $this->handler;
    }

    /**
     * @return CacheManager
     */
    public function getCacheManager(): CacheManager
    {
        return $this->cacheManager;
    }

    /**
     * @return RedisHelper
     */
    public function getRedisHelper(): RedisHelper
    {
        return $this->redisHelper;
    }
}
