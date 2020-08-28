<?php

namespace App\Controller\Rest;

use App\Document\AdrecordProduct;
use App\Document\AdtractionProduct;
use App\Document\AwinProduct;
use App\Exception\ValidatorException;
use App\Repository\CategoryConfigurationsRepository;
use App\Repository\CategoryRepository;
use App\Services\Helpers;
use Doctrine\DBAL\DBALException;
use Doctrine\ODM\MongoDB\DocumentManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class HoverMenuManagmentController extends AbstractRestController
{
    /**
     * @var CategoryConfigurationsRepository
     */
    private $categoryConfRepo;

    /**
     * @var CategoryRepository
     */
    private $categoryRepo;

    /**
     * HoverMenuManagmentController constructor.
     * @param CategoryConfigurationsRepository $categoryConfRepo
     * @param CategoryRepository $categoryRepo
     */
    public function __construct(CategoryConfigurationsRepository $categoryConfRepo, CategoryRepository $categoryRepo, Helpers $helpers)
    {
        parent::__construct($helpers);

        $this->categoryConfRepo = $categoryConfRepo;
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * edit Category from Hover Menu.
     *
     * @Rest\Post("/api/hover_menu/edit", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="HoverMenu")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object HoverMenu",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws DBALException
     * @throws ValidatorException
     */
    public function editHoverMenuAction(Request $request)
    {
        $categoryConfigurations = $this->categoryConfRepo->findOneBy(['categoryId' => $request->get('category_id')]);
        $categoryConfigurations
            ->setKeyWords($request->get('pkw'))
            ->setNegativeKeyWords($request->get('nkw'));

        $this->categoryConfRepo->save($categoryConfigurations);
        
        $view = $this->createSuccessResponse(
            ['test' => 1]
        );

        return $view;
    }

    /**
     * get Products.
     *
     * @Rest\Get("/api/hover_menu/th_list", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="HoverMenu")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object HoverMenu",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws DBALException
     * @throws ValidatorException
     */
    public function listThHoverMenuAction(Request $request)
    {
        $keys = [
            'CategoryName',
            'PositiveKeyWords',
            'NegativeKeyWords',
            'Action'
        ];
        $view = $this->createSuccessResponse(
            $keys
        );

        return $view;
    }

    /**
     * get Products.
     *
     * @Rest\Get("/api/hover_menu/list", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="HoverMenu")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object HoverMenu",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws DBALException
     * @throws ValidatorException
     */
    public function listHoverMenuAction(Request $request)
    {
        $data = $this->categoryConfRepo
            ->getHoverMenuCategoryConf($request->query->all());

        $view = $this->createSuccessResponse(
            array_merge(
                [
                    "draw" => $request->query->get('draw'),
                    "recordsTotal" => $this->categoryConfRepo
                        ->getHoverMenuCategoryConf($request->query->all(), true, true),
                    "recordsFiltered" => $this->categoryConfRepo
                        ->getHoverMenuCategoryConf($request->query->all(), true),
                ],
                ['data' => $data]
            )
        );

        return $view;
    }
}