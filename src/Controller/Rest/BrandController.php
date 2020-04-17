<?php

namespace App\Controller\Rest;

use App\Entity\Collection\BrandsCollection;
use App\Repository\BrandRepository;
use App\Entity\Brand;
use App\Services\Helpers;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use App\Validation\Constraints\SearchQueryParam;

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
     * @View(statusCode=Response::HTTP_OK)
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
     * @return BrandsCollection
     * @throws DBALException
     */
    public function getBrandsAction(
        ParamFetcher $paramFetcher
    )
    {
        $collection = $this->getBrandRepository()->fullTextSearchByParameterFetcher($paramFetcher);
        $count = $this->getBrandRepository()->fullTextSearchByParameterFetcher($paramFetcher, true);
        $brandsCollection = new BrandsCollection($collection, $count);

        return $brandsCollection;
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
     * @return Brand
     */
    public function getBrandByIdAction(
        Brand $brand
    )
    {
        return $brand;
    }

    /**
     * @return BrandRepository
     */
    public function getBrandRepository(): BrandRepository
    {
        return $this->brandRepository;
    }
}