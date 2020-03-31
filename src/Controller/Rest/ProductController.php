<?php

namespace App\Controller\Rest;

use App\Entity\Collection\ProductsCollection;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use App\Entity\Product;
use App\Entity\Collection\SearchProductCollection;

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
     *     description="Json collection object Products",
     *     @SWG\Schema(
     *         type="object",
     *         properties={
     *             @SWG\Property(
     *                  property="collection",
     *                  type="array",
     *                  @SWG\Items(
     *                        type="object",
     *                      @SWG\Property(property="id", type="integer"),
     *                      @SWG\Property(property="sku", type="string"),
     *                      @SWG\Property(property="name", type="string"),
     *                      @SWG\Property(property="description", type="string"),
     *                      @SWG\Property(property="category", type="string"),
     *                      @SWG\Property(property="price", type="string"),
     *                      @SWG\Property(property="shipping", type="string"),
     *                      @SWG\Property(property="currency", type="string"),
     *                      @SWG\Property(property="instock", type="string"),
     *                      @SWG\Property(property="productUrl", type="string"),
     *                      @SWG\Property(property="imageUrl", type="string"),
     *                      @SWG\Property(property="trackingUrl", type="string"),
     *                      @SWG\Property(property="brand", type="string"),
     *                      @SWG\Property(property="originalPrice", type="string"),
     *                      @SWG\Property(property="ean", type="string"),
     *                      @SWG\Property(property="manufacturerArticleNumber", type="string"),
     *                      @SWG\Property(property="extras", type="string"),
     *                      @SWG\Property(property="createdAt", type="string"),
     *                      @SWG\Property(property="rank", type="string"),
     *                      @SWG\Property(property="brandRelationId", type="integer"),
     *                      @SWG\Property(property="categoryIds", type="string")
     *                  )
     *             ),
     *             @SWG\Property(property="count", type="integer")
     *         }
     *     )
     * )
     *
     * @return SearchProductCollection
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getProductsAction(ParamFetcher $paramFetcher)
    {
        $collection = $this->getProductRepository()->fullTextSearch($paramFetcher);
        $count = $this->getProductRepository()->fullTextSearch($paramFetcher, true);

        return (new SearchProductCollection($collection, $count));
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
     *     description="Json object Products",
     *     @Model(type=Product::class, groups={Product::SERIALIZED_GROUP_LIST})
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
     * get Products by ids.
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
     *     description="Json collection object Products",
     *     @SWG\Schema(ref=@Model(type=ProductsCollection::class, groups={Product::SERIALIZED_GROUP_LIST}))
     * )
     *
     * @return ProductsCollection
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getProductByIdsAction(ParamFetcher $paramFetcher)
    {
        $collection = $this->getProductRepository()->getProductByIds($paramFetcher);
        $count = $this->getProductRepository()->getProductByIds($paramFetcher, true);

        return (new ProductsCollection($collection, $count));
    }


    /**
     * @return ProductRepository
     */
    public function getProductRepository(): ProductRepository
    {
        return $this->productRepository;
    }
}