<?php

namespace App\Controller\Rest;

use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use App\Entity\Brand;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
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
     */
    public function __construct(BrandRepository $brandRepository)
    {
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
     *     requirements="^[A-Za-z0-9 éäöåÉÄÖÅ]*$",
     *     nullable=true,
     *     description="Search by each world with `or` condition by name fields")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View(serializerGroups={Brand::SERIALIZED_GROUP_LIST}, statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Brand")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection objects Brand"
     * )
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getBrandsAction(ParamFetcher $paramFetcher)
    {
        $collection = $this->getBrandRepository()->getBrandList($paramFetcher);
        $count = $this->getBrandRepository()->getBrandList($paramFetcher, true);
        return [
            'collection' => $collection,
            'count' => $count
        ];
    }

    /**
     * @return BrandRepository
     */
    public function getBrandRepository(): BrandRepository
    {
        return $this->brandRepository;
    }
}