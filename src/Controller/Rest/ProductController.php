<?php

namespace App\Controller\Rest;

use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use App\Entity\Product;

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
     * @Rest\QueryParam(map=true, name="category_ids", nullable=true, strict=true, requirements="\d+", default="0", description="List of category ids")
     * @Rest\QueryParam(map=true, name="brand_ids", nullable=true, strict=true, requirements="\d+", default="0", description="List of brand ids")
     * @Rest\QueryParam(
     *     name="search",
     *     strict=true,
     *     requirements="^[A-Za-z0-9 éäöåÉÄÖÅ]*$",
     *     nullable=true,
     *     description="Search by each world with `or` condition by sku, name, description, category, brand and price fields")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Products")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection objects Products"
     * )
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getProductsAction(ParamFetcher $paramFetcher)
    {
        $collection = $this->getProductRepository()->fullTextSearch($paramFetcher);
        $count = $this->getProductRepository()->fullTextSearch($paramFetcher, true);
        return [
            'collection' => $collection,
            'count' => $count
        ];
    }

    /**
     * get Product.
     *
     * @Rest\Get("/api/product/{id}")
     *
     * @View(serializerGroups={Product::SERIALIZED_GROUP_LIST}, statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Products")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection objects Products"
     * )
     *
     * @return Product
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getProductByIdAction(Product $product)
    {
        return $product;
    }

    /**
     * get Product.
     *
     * @Rest\Get("/api/products/by/ids")
     *
     * @View(serializerGroups={Product::SERIALIZED_GROUP_LIST}, statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Products")
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
     *     description="Json collection objects Products"
     * )
     *
     * @return Product[]|[]
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getProductByIdsAction(ParamFetcher $paramFetcher)
    {
        $collection = $this->getProductRepository()->getProductByIds($paramFetcher);
        $count = $this->getProductRepository()->getProductByIds($paramFetcher, true);
        return [
            'collection' => $collection,
            'count' => $count
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