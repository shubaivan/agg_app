<?php

namespace App\Controller\Rest;

use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use App\Entity\Product;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractRestController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * ProductController constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * get Products.
     *
     * @Rest\Get("/api/products")
     *
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View(serializerGroups={Product::SERIALIZED_GROUP_LIST}, statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="rewards")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json object with all user meta data or a json string with the value of the requested field"
     * )
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getProductsAction(ParamFetcher $paramFetcher)
    {
        return
            [
                'collection' => $this->getProductRepository()->getProductsList($paramFetcher),
                'count' => $this->getProductRepository()->getProductsList($paramFetcher, true)
            ];
    }

    /**
     * @return ProductRepository
     */
    public function getProductRepository(): ProductRepository
    {
        return $this->productRepository;
    }
}