<?php

namespace App\Controller;

use App\Cache\CacheManager;
use App\Entity\Shop;
use App\Services\HandleDownloadFileData;
use App\Services\ObjectsHandler;
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
        $statisticByShops = [];

        $statisticByDate = $this->getRedisHelper()
            ->keys(Shop::PREFIX_HASH . '*');
        $prepareDataTh = [];
        $prepareDataBody = [];
        foreach ($statisticByDate as $redisUniqKey) {
            $explodeData = explode(':', $redisUniqKey);
            if (is_array($explodeData) && isset($explodeData[0]) && isset($explodeData[1])){
                $dateIterations = explode('_', $explodeData[1]);
                if (is_array($dateIterations) && isset($dateIterations[0]) && isset($dateIterations[1])){
                    $prepareDataTh[$dateIterations[0]][$dateIterations[1]] = $explodeData[1];
                    $prepareDataBody[] = $explodeData[1];

                }
            }
        }
        $resultData = [];
        foreach ($prepareDataBody as $redisUniqKey)
        {
            $startTime = $this->getRedisHelper()
                ->hGetAll(HandleDownloadFileData::TIME_SPEND_PRODUCTS_SHOP_START . $redisUniqKey);
            $endTime = $this->getRedisHelper()
                ->hGetAll(HandleDownloadFileData::TIME_SPEND_PRODUCTS_SHOP_END . $redisUniqKey);
            $resultData[$redisUniqKey]['timeSpent'] = (max($endTime) - min($startTime)) / 60 . ' minutes';

            $counterData = $this->getRedisHelper()
                ->hGetAll(Shop::PREFIX_HASH . $redisUniqKey);

            foreach ($counterData as $hashKey => $hashValue) {
                $explode = explode(':', $hashKey);
                if (count($explode) === 4) {
                    $prefixName = $explode[1];
                    $blockName = $explode[2];
                    $filePath = $explode[3];
                    $explodeFilepath = explode('/', $filePath);
                    array_pop($explodeFilepath);
                    $shopName = array_pop($explodeFilepath);
                    $resourceName = array_pop($explodeFilepath);

                    $resultData[$redisUniqKey]['products_info']
                        [$resourceName][$prefixName][$blockName][$shopName] = $hashValue;
                }
            }
        }


//        foreach ($statisticByDate as $iter=>$keyDateStamp) {
//            $allHashKeys = $this->getRedisHelper()->hGetAll($keyDateStamp);
//
//            if (preg_match('/([^:]*)$/', $keyDateStamp, $matches) > 0) {
//                $date = array_shift($matches);
//                $startTime = $this->getRedisHelper()->hGetAll(HandleDownloadFileData::TIME_SPEND_PRODUCTS_SHOP_START . $date);
//                $endTime = $this->getRedisHelper()->hGetAll(HandleDownloadFileData::TIME_SPEND_PRODUCTS_SHOP_END . $date);
//                $summarize = [];
//                foreach ($allHashKeys as $hashKey => $hashValue) {
//                    $explode = explode(':', $hashKey);
//                    if (count($explode) === 4) {
//                        $prefixName = $explode[1];
//                        $blockName = $explode[2];
//                        $filePath = $explode[3];
//                        $explodeFilepath = explode('/', $filePath);
//                        array_pop($explodeFilepath);
//                        $shopName = array_pop($explodeFilepath);
//                        $resourceName = array_pop($explodeFilepath);
//
//                        if (isset($startTime[$filePath]) && isset($endTime[$filePath])) {
//                            $timeSpent = abs($endTime[$filePath] - $startTime[$filePath]);
//                            if (!isset($summarize[$date][ucfirst($blockName)]['sumTimeSpen']) && $blockName != 'failed') {
//                                $summarize[$date][ucfirst($blockName)]['sumTimeSpent'] = (max($endTime) - min($startTime)) / 60 . ' minutes';
//                            }
//                            $statisticByShops[$date][ucfirst($blockName)][$prefixName][$filePath]['start_time'] = date('m/d/Y H:i:s', $startTime[$filePath]);
//                            $statisticByShops[$date][ucfirst($blockName)][$prefixName][$filePath]['end_time'] = date('m/d/Y H:i:s', $endTime[$filePath]);
//                            $statisticByShops[$date][ucfirst($blockName)][$prefixName][$filePath]['during_time'] = $timeSpent/60 . ' minutes';
//                        }
//                        $statisticByShops[$date][ucfirst($blockName)][$prefixName][$filePath]['resource_name'] = $resourceName;
//                        $statisticByShops[$date][ucfirst($blockName)][$prefixName][$filePath]['shop_name'] = $shopName;
//
//                        $countProducts = ($blockName == 'failed') ? $hashValue / 3 : $hashValue;
//                        $statisticByShops[$date][ucfirst($blockName)][$prefixName][$filePath]['value'] = $countProducts;
//                        if (!isset($summarize[$date][ucfirst($blockName)][$prefixName]['sumProducts'])) {
//                            $summarize[$date][ucfirst($blockName)][$prefixName]['sumProducts'] = 0;
//                        }
//
//                        $summarize[$date][ucfirst($blockName)][$prefixName]['sumProducts'] += $countProducts;
//                    }
//                }
//            }
//        }
//        foreach ($statisticByShops as $dateKey=>$stat) {
//            if (isset($summarize[$dateKey])) {
//                $statisticByShops[$dateKey]['summarize'] = $summarize[$dateKey];
//            }
//        }
//        uksort($statisticByShops, function ($a, $b) {
//            $dateTimeA= \DateTime::createFromFormat('Ymd', $a);
//            $dateTimeB= \DateTime::createFromFormat('Ymd', $b);
//            return $dateTimeA > $dateTimeB ? -1 : 1;
//        });

        return $this->render('statistics/statistics.html.twig', [
            'prepareDataTh' => $prepareDataTh,
            'resultData' => $resultData
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
