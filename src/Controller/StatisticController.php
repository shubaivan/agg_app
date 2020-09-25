<?php

namespace App\Controller;

use App\Cache\CacheManager;
use App\Entity\Product;
use App\Entity\Shop;
use App\Services\HandleDownloadFileData;
use App\Services\ObjectsHandler;
use App\Services\StatisticsService;
use App\Util\RedisHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
     * @var StatisticsService
     */
    private $statisticsService;

    /**
     * FileDownloadController constructor.
     * @param ObjectsHandler $handler
     * @param CacheManager $cacheManager
     * @param RedisHelper $redisHelper
     * @param StatisticsService $statisticsService
     */
    public function __construct(
        ObjectsHandler $handler,
        CacheManager $cacheManager,
        RedisHelper $redisHelper,
        StatisticsService $statisticsService
    )
    {
        $this->handler = $handler;
        $this->cacheManager = $cacheManager;
        $this->redisHelper = $redisHelper;
        $this->statisticsService = $statisticsService;
    }


    /**
     * @Route(name="clear_pools_cache", path="cache/clear/pools")
     */
    public function clearAllPoolsCacheAction()
    {
        $this->getDoctrine()->getRepository(Product::class)
            ->autoVACUUM();

        $this->getCacheManager()->clearAllPoolsCache();
        $articleContent = <<<EOF
**successful** all keay was removed.
EOF;
        return new Response('<html><body>' . $articleContent .'</body></html>');
    }

    /**
     * @Route(name="statistics", path="statistics")
     * @throws \Exception
     */
    public function bufferTestAction()
    {
        $allStatistics = $this->statisticsService->getAllStatistics();
        return $this->render('statistics/statistics.html.twig', [
            'prepareDataTh' => $allStatistics['prepareDataTh'],
            'resultData' => $allStatistics['resultData'],
            'quantityResult' => $allStatistics['quantityResult']
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
