<?php

namespace App\Controller\Rest\Admin;

use App\Cache\TagAwareQueryResultCacheBrand;
use App\Cache\TagAwareQueryResultCacheProduct;
use App\Entity\Brand;
use App\Repository\AdminShopsRulesRepository;
use App\Repository\CategoryConfigurationsRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
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
     * AdminShopRuleController constructor.
     * @param AdminShopsRulesRepository $adminShopsRulesRepository
     * @param Helpers $helpers
     */
    public function __construct(AdminShopsRulesRepository $adminShopsRulesRepository, Helpers $helpers)
    {
        parent::__construct($helpers);
        $this->adminShopsRulesRepository = $adminShopsRulesRepository;
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
     * @throws NoResultException
     * @throws NonUniqueResultException
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
}