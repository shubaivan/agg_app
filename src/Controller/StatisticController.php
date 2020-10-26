<?php

namespace App\Controller;

use App\Cache\CacheManager;
use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\Strategies;
use App\Kernel;
use App\Services\HandleDownloadFileData;
use App\Services\Models\Shops\Strategies\CutSomeWordsFromProductNameByDelimiter;
use App\Services\ObjectsHandler;
use App\Services\StatisticsService;
use App\Util\RedisHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
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
     * @var Kernel
     */
    private $kernel;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * FileDownloadController constructor.
     * @param ObjectsHandler $handler
     * @param CacheManager $cacheManager
     * @param RedisHelper $redisHelper
     * @param StatisticsService $statisticsService
     * @param KernelInterface $kernel
     * @param EntityManagerInterface $em
     */
    public function __construct(
        ObjectsHandler $handler,
        CacheManager $cacheManager,
        RedisHelper $redisHelper,
        StatisticsService $statisticsService,
        KernelInterface $kernel,
        EntityManagerInterface $em
    )
    {
        $this->handler = $handler;
        $this->cacheManager = $cacheManager;
        $this->redisHelper = $redisHelper;
        $this->statisticsService = $statisticsService;
        $this->kernel = $kernel;
        $this->em = $em;
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
        $dir = $this->kernel->getProjectDir() . '/src/Services/Models/Shops/Strategies';
        $array_diff = array_diff(scandir($dir), array('..', '.'));
        foreach ($array_diff as $item) {
            $preg_replace = preg_replace("/\..+/", "", $item);
            if (!is_dir($dir.'/'.$preg_replace)) {
                $argument = 'App\Services\Models\Shops\Strategies\\' . $preg_replace;
                $r = new \ReflectionClass($argument);
                $requireProperties = $r->newInstanceWithoutConstructor()::requireProperty();
                $value = [];
                foreach ($requireProperties as $property) {
                    $value[$property] = $r->getProperty($property)->getValue();
                }
                $value['strategyNameSpace'] = $argument;
                $value['strategyName'] = $preg_replace;

                $handleObject = $this->handler->handleObject(
                    $value,
                    Strategies::class
                );
                $this->em->persist($handleObject);
            }
        }
        $this->em->flush();
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
