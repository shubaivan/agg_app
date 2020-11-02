<?php

namespace App\Controller\Rest\Admin;

use App\Cache\TagAwareQueryResultCacheShop;
use App\Cache\TagAwareQuerySecondLevelCacheShop;
use App\Controller\Admin\ResourceShopManagmentController;
use App\Entity\Category;
use App\Entity\ManuallyResourceJob;
use App\Entity\Shop;
use App\Entity\User;
use App\QueueModel\ManuallyResourceJobs;
use App\Repository\BrandRepository;
use App\Repository\CategoryConfigurationsRepository;
use App\Repository\FilesRepository;
use App\Repository\ManuallyResourceJobRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use App\Services\Admin\ResourceShopManagement;
use App\Services\Models\CategoryService;
use App\Services\ObjectsHandler;
use App\Util\AmqpHelper;
use App\Util\RedisHelper;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Controller\Rest\AbstractRestController;
use App\Services\Helpers;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\DoctrineProvider;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\TraceableMessageBus;

class ResourceShopsController extends AbstractRestController
{
    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var AmqpHelper
     */
    private $amqpHelper;

    /**
     * @var RedisHelper
     */
    private $redisHelper;

    /**
     * @var ResourceShopManagement
     */
    private $resourceShopManagement;

    /**
     * @var ObjectsHandler
     */
    private $objectsHandler;

    /**
     * @var ContainerBagInterface
     */
    private $params;

    /**
     * @var array
     */
    private $urlsData = [];

    /**
     * @var ManuallyResourceJobRepository
     */
    private $manuallyResourceJobRepository;

    /**
     * @var TraceableMessageBus
     */
    private $bus;

    /**
     * @var TagAwareQuerySecondLevelCacheShop
     */
    private $tagAwareQuerySecondLevelCacheShop;

    /**
     * @var FilesRepository
     */
    private $fileRepo;

    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * @var TagAwareQueryResultCacheShop
     */
    private $tagAwareQueryResultCacheShop;

    /**
     * ResourceShopsController constructor.
     * @param Helpers $helpers
     * @param ShopRepository $shopRepository
     * @param ProductRepository $productRepository
     * @param AmqpHelper $amqpHelper
     * @param RedisHelper $redisHelper
     * @param ResourceShopManagement $resourceShopManagement
     * @param ContainerBagInterface $params
     * @param ManuallyResourceJobRepository $manuallyResourceJobRepository
     * @param ObjectsHandler $objectsHandler
     * @param MessageBusInterface $manuallyBus
     * @param TagAwareQuerySecondLevelCacheShop $tagAwareQuerySecondLevelCacheShop
     * @param FilesRepository $fileRepo
     * @param CategoryService $categoryService
     * @param TagAwareQueryResultCacheShop $tagAwareQueryResultCacheShop
     */
    public function __construct(
        Helpers $helpers,
        ShopRepository $shopRepository,
        ProductRepository $productRepository,
        AmqpHelper $amqpHelper,
        RedisHelper $redisHelper,
        ResourceShopManagement $resourceShopManagement,
        ContainerBagInterface $params,
        ManuallyResourceJobRepository $manuallyResourceJobRepository,
        ObjectsHandler $objectsHandler,
        MessageBusInterface $manuallyBus,
        TagAwareQuerySecondLevelCacheShop $tagAwareQuerySecondLevelCacheShop,
        FilesRepository $fileRepo,
        CategoryService $categoryService,
        TagAwareQueryResultCacheShop $tagAwareQueryResultCacheShop
    )
    {
        parent::__construct($helpers);
        $this->params = $params;
        $this->shopRepository = $shopRepository;
        $this->productRepository = $productRepository;
        $this->amqpHelper = $amqpHelper;
        $this->redisHelper = $redisHelper;
        $this->resourceShopManagement = $resourceShopManagement;
        $this->manuallyResourceJobRepository = $manuallyResourceJobRepository;
        $this->objectsHandler = $objectsHandler;
        $this->bus = $manuallyBus;
        $this->tagAwareQuerySecondLevelCacheShop = $tagAwareQuerySecondLevelCacheShop;
        $this->fileRepo = $fileRepo;
        $this->categoryService = $categoryService;
        $this->tagAwareQueryResultCacheShop = $tagAwareQueryResultCacheShop;

        $this->urlsData = [
            $params->get('adrecord_download_file_path') => $this->params->get('adrecord_download_urls'),
            $params->get('adtraction_download_file_path') => $this->params->get('adtraction_download_urls'),
            $params->get('awin_download_file_path') => $this->params->get('awin_download_urls'),
            $params->get('tradedoubler_download_file_path') => $this->params->get('tradedoubler_download_urls')
        ];
    }

    /**
     * get Shop set.
     *
     * @Rest\Post("/admin/api/resource/shops", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function resourceShopsAction(Request $request)
    {
        $parameterBag = new ParameterBag($request->request->all());
        $data = $this->shopRepository
            ->getShopsForSelect2($parameterBag);
        $mapData = array_map(function ($v) {
            switch (true) {
                case isset(Shop::$shopNamesTradeDoublerMapping[Shop::getMapShopKeyByOriginalName($v['text'])]):
                    $v['resource_relation'] = Shop::TRADE_DOUBLER;
                    break;
                case isset(Shop::$shopNamesAwinMapping[Shop::getMapShopKeyByOriginalName($v['text'])]):
                    $v['resource_relation'] = Shop::AWIN;
                    break;
                case isset(Shop::$shopNamesAdtractionMapping[Shop::getMapShopKeyByOriginalName($v['text'])]):
                    $v['resource_relation'] = Shop::ADTRACTION;
                    break;
                case isset(Shop::$shopNamesAdrecordMapping[Shop::getMapShopKeyByOriginalName($v['text'])]):
                    $v['resource_relation'] = Shop::ADRECORD;
                    break;
                default:
                    break;
            }

            return $v;
        }, $data);
        $more = $parameterBag->get('page') * 25 < $this->shopRepository
                ->getShopsForSelect2($parameterBag, true);
        $view = $this->createSuccessResponse(
            array_merge(
                [
                    "pagination" => [
                        'more' => $more
                    ],
                ],
                ['results' => $mapData]
            )
        );

        return $view;
    }

    /**
     * get Shop list.
     *
     * @Rest\Post("/admin/api/resource/resource_list", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function resourceListAction(Request $request)
    {
        $shopNamesMapping = Shop::getGroupShopNamesMapping();
        $resourceList = array_keys($shopNamesMapping);

        $view = $this->createSuccessResponse($resourceList);

        return $view;
    }

    /**
     * run Shop reloading product.
     *
     * @Rest\Post("/admin/api/resource/shop/reloading", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws \Exception
     * @throws \League\Flysystem\FileExistsException
     * @throws \Throwable
     */
    public function shopReloadingAction(Request $request)
    {
        $var = $request->request->get('shopName');
        $mapShopKeyByOriginalName = Shop::getMapShopKeyByOriginalName($var);
        if (!$mapShopKeyByOriginalName) {
            throw new \Exception('shop not exist in resources map');
        }
        $url = '';
        $dirForFiles = '';
        $urlsData = $this->getUrlsData();
        $array_filter = array_filter($urlsData, function ($mv, $mk) use ($mapShopKeyByOriginalName, &$url, &$dirForFiles) {
            $array_filter = array_filter($mv, function ($v, $k) use ($mapShopKeyByOriginalName, &$url) {
                if($k == $mapShopKeyByOriginalName) {
                    $url = $v;
                    return true;
                }
                return false;
            }, ARRAY_FILTER_USE_BOTH);
            if (count($array_filter)) {
                $dirForFiles = $mk;
            }
            return count($array_filter);
        }, ARRAY_FILTER_USE_BOTH);

        if (!count($array_filter)) {
            throw new \Exception('url was not found');
        }

        $this->redisHelper
            ->hIncrBy('attempt', date('Ymd'));
        $redisUniqKey = date('Ymd') . '_' . $this->redisHelper->hGet('attempt', date('Ymd'));

        $manuallyResourceJob = new ManuallyResourceJob();
        $user = $this->getUser();
        if ($user instanceof User) {
            $manuallyResourceJob->setCreatedAtAdmin($user);
        }
        $manuallyResourceJob
            ->setShopKey($mapShopKeyByOriginalName)
            ->setDirForFiles($dirForFiles)
            ->setUrl($url)
            ->setRedisUniqKey($redisUniqKey);
    
        $this->objectsHandler
            ->validateEntity($manuallyResourceJob);

        $this->manuallyResourceJobRepository
            ->save($manuallyResourceJob);
        $manuallyResourceJobs = new ManuallyResourceJobs($manuallyResourceJob->getId());
        $this->bus
            ->dispatch($manuallyResourceJobs);

        $view = $this->createSuccessResponse([$mapShopKeyByOriginalName]);

        return $view;
    }

    /**
     * edit Shop.
     *
     * @Rest\Post("/admin/api/shop/edit", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws InvalidArgumentException
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function editShopAction(Request $request)
    {
        /** @var Registry $registry */
        $registry = $this->get('doctrine');
        $objectManager = $registry->getManager();
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $objectManager->getConnection();
        $connection->beginTransaction(); // suspend auto-commit
        try{
            $shop = $this->shopRepository
                ->findOneBy(['id' => $request->get('shop_id')]);
            if (!$shop) {
                throw new \Exception('shop was not found');
            }
            if ($request->get('category_ids')) {
                $this->categoryService->createShopCategoryModel(
                    $shop, $request->get('category_ids')
                );
            } else {
                $shop->getCategoryRelation()->clear();
            }

            $fileIds = $request->get('file_ids');
            if (is_array($fileIds) && count($fileIds)) {
                $files = $this->fileRepo
                    ->getByIds($fileIds);
                foreach ($files as $file) {
                    $file->setShop($shop);
                }
            }
            $objectManager->flush();
            $connection->commit();
            $this->tagAwareQueryResultCacheShop
                ->getTagAwareAdapter()
                ->invalidateTags([
                    ShopRepository::SHOP_FULL_TEXT_SEARCH
                ]);

            $this->tagAwareQuerySecondLevelCacheShop
                ->deleteAll();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        $view = $this->createSuccessResponse(
            ['test' => 1]
        );

        return $view;
    }

    /**
     * get resource shop list.
     *
     * @Rest\Post("/admin/api/resource/shop_list", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function resourceShopListAction(Request $request)
    {
        $parameterBag = $this->shopRepository->handleDataTablesRequest($request->request->all());
        $th = ResourceShopManagmentController::DATA_TABLES_TH;
        $result = [];
        $shopNamesMapping = Shop::getGroupShopNamesMapping();
        foreach ($shopNamesMapping as $resource => $shopList) {
            if ($parameterBag->get('ResourceName') && $parameterBag->get('ResourceName') !== $resource) {
                continue;
            }
            foreach ($shopList as $shop) {
                $shopThData = [];

                $shopTh = [$resource, $shop, '', 0, '', 'reloading'];
                foreach ($shopTh as $key => $tdData) {
                    $shopThData[$th[$key]] = $tdData;
                }
                $result[] = $shopThData;
            }
        }
        $commonCount = count($result);
        $count = count($result);
        if ($parameterBag->get('search')) {
            $search = $parameterBag->get('search');
            $result = array_filter($result, function ($a) use ($search) {
                return preg_grep(
                    "/$search/iu", $a
                );
            });
            $count = count($result);
        }
        if ($parameterBag->get('sort_by')) {
            $result = $this->arrayOrderBy($result, $parameterBag->get('sort_by') . ' ' . $parameterBag->get('sort_order'));
        }

        if ($parameterBag->get('offset') !== false && $parameterBag->get('limit') !== false) {
            $result = array_slice($result, $parameterBag->get('offset'), $parameterBag->get('limit'));
        }

        $quantityResult = [];
        foreach (Shop::queueListName() as $queue => $resourceName) {
            $quantityResult[$resourceName] = $this->amqpHelper
                ->getQuantityJobsQueue($queue);
        }
        $shopNames = [];
        $responseResult = [];
        $result = array_map(function ($v, $k) use ($quantityResult, &$shopNames, &$responseResult) {
            $v[ResourceShopManagmentController::PRODUCTS_QUANTITY] = $this->productRepository
                ->getCountGroupedProductsByShop($v[ResourceShopManagmentController::SHOP_NAME]);
            if (isset($quantityResult[$v[ResourceShopManagmentController::RESOURCE_NAME]])) {
                $v['queue'] = $quantityResult[$v[ResourceShopManagmentController::RESOURCE_NAME]];
                $shopKey = Shop::getMapShopKeyByOriginalName($v[ResourceShopManagmentController::SHOP_NAME]);
                $shopNames[$shopKey] = $v[ResourceShopManagmentController::SHOP_NAME];
                /** @var ManuallyResourceJob[] $manuallyResourceJob */
                $manuallyResourceJob = $this->manuallyResourceJobRepository
                    ->findBy(['shopKey' => $shopKey]);
                $manuallyResourceJob = array_map(function ($v) {
                    /** @var $v ManuallyResourceJob */
                    return [
                        'createdAt' => $v->getCreatedAt(),
                        'createdAtAdmin' => [
                            'email' => $v->getCreatedAtAdmin()
                                ? $v->getCreatedAtAdmin()->getEmail()
                                : null
                        ],
                        'enumStatusPresent' => $v->getStoreNamesValue()
                    ];
                }, $manuallyResourceJob);
                $v[ResourceShopManagmentController::MANUALLY_JOBS] = $manuallyResourceJob;
            }
            $responseResult[$v[ResourceShopManagmentController::SHOP_NAME]] = $v;
            return $v;
        }, $result, array_keys($result));
        $parameterBagShopNames = new ParameterBag();
        $parameterBagShopNames->set('names', $shopNames);
        $shops = $this->shopRepository->getShopsByNames($parameterBagShopNames);
        foreach ($shops as $shopFromDB) {
            $shopName = $shopFromDB->getShopName();
            if (isset($responseResult[$shopName])) {
                $responseResult[$shopName]['shop_from_db']['shop_id'] = $shopFromDB->getId();
                $responseResult[$shopName]['shop_from_db']['category_models'] = $shopFromDB->getCategoryRelation()
                    ->map(function (Category $category) {
                        return [
                            'id' => $category->getId(),
                            'text' => $category->getCategoryName(),
                            'slug' => $category->getSlug(),
                            'hotCategory' => $category->getHotCategory(),
                            'disableForParsing' => $category->getDisableForParsing(),
                            'sectionRelation' => $category->getSectionRelation()
                                ? $category->getSectionRelation()->getId()
                                : null
                        ];
                    });
                $responseResult[$shopName][ResourceShopManagmentController::ACTION] .= ',edit';
                $responseResult[$shopName][ResourceShopManagmentController::FILES] = $this
                    ->fileRepo->getByShop($shopFromDB);
            }
        }
        $view = $this->createSuccessResponse(
            array_merge(
                [
                    "draw" => $request->request->get('draw'),
                    "recordsTotal" => $commonCount,
                    "recordsFiltered" => $count
                ],
                ['data' => array_values($responseResult)]
            )
        );

        return $view;
    }

    private function arrayOrderBy(array &$arr, $order = null)
    {
        if (is_null($order)) {
            return $arr;
        }
        $orders = explode(',', $order);
        usort($arr, function ($a, $b) use ($orders) {
            $result = array();
            foreach ($orders as $value) {
                list($field, $sort) = array_map('trim', explode(' ', trim($value)));
                if (!(isset($a[$field]) && isset($b[$field]))) {
                    continue;
                }
                if (strcasecmp($sort, 'desc') === 0) {
                    $tmp = $a;
                    $a = $b;
                    $b = $tmp;
                }
                if (is_numeric($a[$field]) && is_numeric($b[$field])) {
                    $result[] = $a[$field] - $b[$field];
                } else {
                    $result[] = strcmp($a[$field], $b[$field]);
                }
            }
            return implode('', $result);
        });
        return $arr;
    }

    /**
     * @return array
     */
    private function getUrlsData(): array
    {
        return $this->urlsData;
    }
}