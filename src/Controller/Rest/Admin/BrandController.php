<?php

namespace App\Controller\Rest\Admin;

use App\Entity\Brand;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Controller\Rest\AbstractRestController;
use App\Document\TradeDoublerProduct;
use App\Exception\ValidatorException;
use App\Repository\BrandRepository;
use App\Services\Helpers;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class BrandController extends AbstractRestController
{
    /**
     * @var BrandRepository
     */
    private $brandRepository;

    /**
     * BrandController constructor.
     * @param BrandRepository $brandRepository
     * @param Helpers $helpers
     */
    public function __construct(BrandRepository $brandRepository, Helpers $helpers)
    {
        parent::__construct($helpers);

        $this->brandRepository = $brandRepository;
    }
    
    /**
     * get Brands.
     *
     * @Rest\Post("/admin/api/brand_list", options={"expose": true})
     *
     * @param Request $request
     * @param ParamFetcher $paramFetcher
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
     * @throws DBALException
     * @throws ValidatorException
     */
    public function postBrandListAction(Request $request, ParamFetcher $paramFetcher)
    {
        $t = 1;
        $dataTable = $this->brandRepository
            ->getDataTablesData($request->request->all());

        $view = $this->createSuccessResponse(
            array_merge(
                [
                    "draw" => $request->request->get('draw'),
                    "recordsTotal" => $this->brandRepository
                        ->getDataTablesData($request->request->all(), true, true),
                    "recordsFiltered"=> $this->brandRepository
                        ->getDataTablesData($request->request->all(), true)
                ],
                ['data' => $dataTable]
            ),
            [Brand::SERIALIZED_GROUP_LIST]
        );

        return $view;
    }
}