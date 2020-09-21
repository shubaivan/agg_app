<?php


namespace App\Services;


use App\Entity\Shop;
use App\Util\RedisHelper;
use Twig\Environment;

class StatisticsService
{
    /**
     * @var RedisHelper
     */
    private $redisHelper;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * StatisticsService constructor.
     * @param RedisHelper $redisHelper
     * @param Environment $twig
     */
    public function __construct(RedisHelper $redisHelper, Environment $twig)
    {
        $this->redisHelper = $redisHelper;
        $this->twig = $twig;
    }


    /**
     * @throws \Exception
     */
    public function getAllStatistics()
    {
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
            if (is_array($endTime) && count($endTime) > 0 && is_array($startTime) && count($startTime) > 0) {
                $resultData[$redisUniqKey]['timeSpent'] = (max($endTime) - min($startTime)) / 60 . ' minutes';
            }

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
                    $shopName = Shop::getRealShopNameByKey(array_pop($explodeFilepath));
                    $resourceName = array_pop($explodeFilepath);
                    if ($blockName == 'failed') {
                        if ((int)$hashValue >= 3) {
                            $hashValue = $hashValue/3;
                        } else {
                            continue;
                        }
                    }
                    $resultData[$redisUniqKey]['products_info']
                    [$resourceName][$prefixName][$blockName][$shopName] = $hashValue;
                }
            }
        }

        return [
            'prepareDataTh' => $prepareDataTh,
            'resultData' => $resultData
        ];
    }

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Exception
     */
    public function getHtmlAllStatistics()
    {
        $allStatistics = $this->getAllStatistics();
        return $this->twig->render('partial/staistics_part.html.twig', [
            'prepareDataTh' => $allStatistics['prepareDataTh'],
            'resultData' => $allStatistics['resultData']
        ]);
    }

    /**
     * @return RedisHelper
     */
    public function getRedisHelper(): RedisHelper
    {
        return $this->redisHelper;
    }
}