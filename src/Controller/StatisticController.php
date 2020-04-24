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

        $statisticByDate = $this->getRedisHelper()->keys(Shop::PREFIX_HASH . '*');

        foreach ($statisticByDate as $keyDateStamp) {
            $allHashKeys = $this->getRedisHelper()->hGetAll($keyDateStamp);

            if (preg_match('/([^:]*)$/', $keyDateStamp, $matches) > 0) {
                $date = array_shift($matches);
                foreach ($allHashKeys as $hashKey => $hashValue) {
                    $explode = explode(':', $hashKey);
                    if (count($explode) === 4) {
                        $prefixName = $explode[1];
                        $blockName = $explode[2];
                        $shopName = $explode[3];
                        $statisticByShops[$date][ucfirst($blockName)][$prefixName][$shopName] = $hashValue;
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
