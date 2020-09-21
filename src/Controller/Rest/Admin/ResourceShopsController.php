<?php

namespace App\Controller\Rest\Admin;

use App\Controller\Admin\ResourceShopManagmentController;
use App\Entity\Shop;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use App\Util\AmqpHelper;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Controller\Rest\AbstractRestController;
use App\Services\Helpers;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

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
     * ResourceShopsController constructor.
     * @param ShopRepository $shopRepository
     * @param ProductRepository $productRepository
     * @param AmqpHelper $amqpHelper
     */
    public function __construct(
        Helpers $helpers,
        ShopRepository $shopRepository,
        ProductRepository $productRepository,
        AmqpHelper $amqpHelper
    )
    {
        parent::__construct($helpers);
        $this->shopRepository = $shopRepository;
        $this->productRepository = $productRepository;
        $this->amqpHelper = $amqpHelper;
    }

    /**
     * get Shop list.
     *
     * @Rest\Post("/admin/api/resource_list", options={"expose": true})
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
     * @Rest\Post("/admin/api/shop/reloading", options={"expose": true})
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
     */
    public function shopReloadingAction(Request $request)
    {
        $var = $request->request->get('shopName');
        $mapShopKeyByOriginalName = Shop::getMapShopKeyByOriginalName($var);
        if (!$mapShopKeyByOriginalName) {
            throw new \Exception('shop not exist in resources map');
        }

        $view = $this->createSuccessResponse([$mapShopKeyByOriginalName]);

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
        foreach ($shopNamesMapping as $resource=>$shopList) {
            foreach ($shopList as $shop)
            {
                $shopThData = [];

                $shopTh = [$resource, $shop, 0, 'reloading'];
                  foreach ($shopTh as $key=>$tdData)
                  {
                      $shopThData[$th[$key]] = $tdData;
                  }
                $result[] = $shopThData;
            }
        }
        $commonCount = count($result);
        $count = count($result);
        if ($parameterBag->get('search')) {
            $search = $parameterBag->get('search');
            $result = array_filter($result, function($a) use ($search) {
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
        foreach (Shop::queueListName() as $queue=>$resourceName) {
            $quantityResult[$resourceName] = $this->amqpHelper
                ->getQuantityJobsQueue($queue);
        }
        $result = array_map(function ($v) use ($quantityResult) {
            $v[ResourceShopManagmentController::PRODUCTS_QUANTITY] = $this->productRepository
                ->getCountGroupedProductsByShop($v['ShopName']);
            if (isset($quantityResult[$v[ResourceShopManagmentController::RESOURCE_NAME]])) {
                $v['queue'] = $quantityResult[$v[ResourceShopManagmentController::RESOURCE_NAME]];
            }

            return $v;
        }, $result);
        $view = $this->createSuccessResponse(
            array_merge(
                [
                    "draw" => $request->request->get('draw'),
                    "recordsTotal" => $commonCount,
                    "recordsFiltered"=> $count
                ],
                ['data' => $result]
            )
        );

        return $view;
    }

    private function arrayOrderBy(array &$arr, $order = null) {
        if (is_null($order)) {
            return $arr;
        }
        $orders = explode(',', $order);
        usort($arr, function($a, $b) use($orders) {
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
                if (is_numeric($a[$field]) && is_numeric($b[$field]) ) {
                    $result[] = $a[$field] - $b[$field];
                } else {
                    $result[] = strcmp($a[$field], $b[$field]);
                }
            }
            return implode('', $result);
        });
        return $arr;
    }
}