<?php

namespace App\Controller\Rest\Admin;

use App\Cache\TagAwareQueryResultCacheBrand;
use App\Cache\TagAwareQueryResultCacheProduct;
use App\Entity\AdminShopsRules;
use App\Entity\Brand;
use App\Entity\Collection\Admin\ShopRules\CreateShopRules;
use App\Entity\Collection\Admin\ShopRules\EditShopRules;
use App\Exception\ValidatorException;
use App\Repository\AdminShopsRulesRepository;
use App\Repository\CategoryConfigurationsRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use App\Services\Admin\AdminShopRules;
use App\Services\ObjectsHandler;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Controller\Rest\AbstractRestController;
use App\Repository\BrandRepository;
use App\Services\Helpers;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\DoctrineProvider;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class AdminShopRuleController extends AbstractRestController
{
    /**
     * @var AdminShopsRulesRepository
     */
    private $adminShopsRulesRepository;

    /**
     * @var ObjectsHandler
     */
    private $oh;

    /**
     * @var AdminShopRules
     */
    private $asr;

    /**
     * AdminShopRuleController constructor.
     * @param AdminShopsRulesRepository $adminShopsRulesRepository
     * @param ObjectsHandler $oh
     * @param Helpers $helpers
     * @param AdminShopRules $asr
     */
    public function __construct(
        AdminShopsRulesRepository $adminShopsRulesRepository,
        ObjectsHandler $oh, Helpers $helpers, AdminShopRules $asr)
    {
        parent::__construct($helpers);
        $this->adminShopsRulesRepository = $adminShopsRulesRepository;
        $this->oh = $oh;
        $this->asr = $asr;
    }


    /**
     * get dataTablesData.
     *
     * @Rest\Post("/admin/api/shop_rule_list", options={"expose": true})
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
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function postShopRuleListAction(Request $request)
    {
        $dataTable = $this->adminShopsRulesRepository
            ->getDataTablesData($request->request->all());

        $view = $this->createSuccessResponse(
            array_merge(
                [
                    "draw" => $request->request->get('draw'),
                    "recordsTotal" => $this->adminShopsRulesRepository
                        ->getDataTablesData($request->request->all(), true, true),
                    "recordsFiltered"=> $this->adminShopsRulesRepository
                        ->getDataTablesData($request->request->all(), true)
                ],
                ['data' => $dataTable]
            )
        );

        return $view;
    }

    /**
     * edit Shop Rule for some Shop.
     *
     * @Rest\Post("/admin/api/shop_rules/edit", options={"expose": true})
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
     * @throws ValidatorException
     * @throws \Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function editShopRulesAction(Request $request)
    {
        $t =1;
        $handleObject = $this->getOh()
            ->handleObject(
                $request->request->all(),
                EditShopRules::class
            );
        $this->getAsr()->updateShopRule($handleObject);

        $view = $this->createSuccessResponse(
            ['test' => 1]
        );

        return $view;
    }

    /**
     * edit Shop Rule for some Shop.
     *
     * @Rest\Post("/admin/api/shop_rules/create", options={"expose": true})
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
     * @throws ValidatorException
     * @throws \Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function createShopRulesAction(Request $request)
    {
        /** @var CreateShopRules $handleObject */
        $handleObject = $this->getOh()
            ->handleObject(
                $request->request->all(),
                CreateShopRules::class
            );
        $this->getAsr()->createShopRule($handleObject);

        /** @var Registry $resultCacheImpl */
        $resultCacheImpl = $this->get('doctrine');
        $objectManager = $resultCacheImpl->getManager();
        /** @var Configuration $configuration */
        $configuration = $objectManager->getConfiguration();
        /** @var DoctrineProvider $resultCacheImpl1 */
        $resultCacheImpl1 = $configuration->getResultCacheImpl();
        $resultCacheImpl1->delete(AdminShopsRulesRepository::CACHE_ID_EXCLUDE_SHOP_FOR_RULE_LIST);

        $view = $this->createSuccessResponse(
            ['shopName' => $handleObject->getShopName()]
        );

        return $view;
    }

    /**
     * @return ObjectsHandler
     */
    private function getOh(): ObjectsHandler
    {
        return $this->oh;
    }

    /**
     * @return AdminShopRules
     */
    private function getAsr(): AdminShopRules
    {
        return $this->asr;
    }
}