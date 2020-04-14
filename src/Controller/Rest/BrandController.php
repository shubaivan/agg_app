<?php

namespace App\Controller\Rest;

use App\Entity\Collection\BrandsCollection;
use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use App\Entity\Brand;
use App\Services\Helpers;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
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
     * @Rest\Get("/api/brands")
     *
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
     *     description="Json collection object Brands",
     *     @SWG\Schema(ref=@Model(type=BrandsCollection::class, groups={Brand::SERIALIZED_GROUP_LIST}))
     * )
     *
     * @return BrandsCollection
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws DBALException
     * @throws InvalidArgumentException
     */
    public function getBrandsAction(
        ParamFetcher $paramFetcher
    )
    {
        $collection = $this->getBrandRepository()->getEntityList($paramFetcher);
        $count = $this->getBrandRepository()->getEntityList($paramFetcher, true);
        $brandsCollection = new BrandsCollection($collection, $count);

        return $brandsCollection;
    }

    /**
     * @return BrandRepository
     */
    public function getBrandRepository(): BrandRepository
    {
        return $this->brandRepository;
    }
}