<?php

namespace App\Controller\Rest;

use App\Entity\Collection\ProductsCollection;
use App\Entity\Product;
use App\Entity\Brand;
use App\Services\Helpers;
use App\Services\Models\BrandService;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use App\Validation\Constraints\SearchQueryParam;
use App\Entity\Collection\BrandsCollection;

class BrandController extends AbstractRestController
{
    /**
     * @var BrandService
     */
    private $brandService;

    /**
     * BrandController constructor.
     * @param BrandService $brandService
     * @param Helpers $helpers
     */
    public function __construct(
        BrandService $brandService,
        Helpers $helpers
    )
    {
        parent::__construct($helpers);
        $this->brandService = $brandService;
    }

    /**
     * get Brands.
     *
     * @Rest\Get("/api/brands")
     *
     * @Rest\QueryParam(
     *     name="search",
     *     strict=true,
     *     requirements=@SearchQueryParam,
     *     nullable=true,
     *     description="Search by each sentence/world separatly delimetery which eqaul ',', with `or` condition by sku, name, description, category, brand, shop and price fields")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @SWG\Tag(name="Brand")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection objects",
     *     @SWG\Schema(
     *         type="object",
     *         properties={
     *             @SWG\Property(
     *                  property="collection",
     *                  type="array",
     *                  @SWG\Items(
     *                        type="object",
     *                      @SWG\Property(property="id", type="integer"),
     *                      @SWG\Property(property="name", type="string"),
     *                      @SWG\Property(property="createdAt", type="string")
     *                  )
     *             ),
     *             @SWG\Property(property="count", type="integer")
     *         }
     *     )
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws DBALException
     * @throws \Exception
     */
    public function getBrandsAction(
        ParamFetcher $paramFetcher
    )
    {
        $brandsCollection = $this->getBrandService()->getBrandsByFilter($paramFetcher);
        $view = $this->createSuccessResponse($brandsCollection);
        $view
            ->getResponse()
            ->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }

    /**
     * get Brand by id.
     *
     * @Rest\Get("/api/brand/{id}", requirements={"id"="\d+"})
     *
     * @View(serializerGroups={Brand::SERIALIZED_GROUP_LIST}, statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Brand")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json object with relation items",
     *     @Model(type=Brand::class, groups={Brand::SERIALIZED_GROUP_LIST}))
     *     )
     * )
     *
     * @param Brand $brand
     *
     * @return \FOS\RestBundle\View\View
     * @throws \Exception
     */
    public function getBrandByIdAction(
        Brand $brand
    )
    {
        $view = $this->createSuccessResponse($brand, [Brand::SERIALIZED_GROUP_LIST]);
        $view
            ->getResponse()
            ->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }

    /**
     * get Brand Facet filters.
     *
     * @Rest\Get("/api/brand/facet_filters/{uniqIdentificationQuery}")
     *
     * @Rest\QueryParam(
     *     name="search",
     *     strict=true,
     *     requirements=@SearchQueryParam,
     *     nullable=true,
     *     description="Search by each sentence/world separatly delimetery which eqaul ',', with `or` condition by brand_name fields")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Brand")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object"
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws DBALException
     * @throws \Exception
     */
    public function getBrandsFacetFiltersAction(ParamFetcher $paramFetcher, $uniqIdentificationQuery)
    {
        $brandsCollection = $this->getBrandService()
            ->facetFilters($uniqIdentificationQuery, $paramFetcher);
        $view = $this->createSuccessResponse($brandsCollection);
        $view
            ->getResponse()
            ->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }

    /**
     * get Brands by ids.
     *
     * @Rest\Get("/api/brands/by/ids")
     *
     * @SWG\Tag(name="Brand")
     *
     * @Rest\QueryParam(map=true, name="ids", nullable=false, strict=true, requirements="\d+", default="0", description="List products by ids")
     *
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     *     @SWG\Schema(ref=@Model(type=BrandsCollection::class, groups={Brand::SERIALIZED_GROUP_LIST}))
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function getBrandsByIdsAction(ParamFetcher $paramFetcher)
    {
        $productsCollection = $this->getBrandService()
            ->getBrandsByIds($paramFetcher);
        $view = $this->createSuccessResponse(
            $productsCollection, [Brand::SERIALIZED_GROUP_LIST]
        );
        $view->getResponse()->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }

    /**
     * @return BrandService
     */
    private function getBrandService(): BrandService
    {
        return $this->brandService;
    }
}