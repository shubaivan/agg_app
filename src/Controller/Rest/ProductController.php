<?php

namespace App\Controller\Rest;

use App\Entity\Collection\ProductCollection;
use App\Entity\Collection\ProductsCollection;
use App\Entity\Collection\SearchProductCollection;
use App\Exception\ValidatorException;
use App\Repository\BrandRepository;
use App\Repository\ProductRepository;
use App\Services\Helpers;
use App\Services\Models\ProductService;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use App\Entity\Product;
use App\Validation\Constraints\ExtraFields;
use App\Validation\Constraints\SearchQueryParam;

class ProductController extends AbstractRestController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * ProductController constructor.
     * @param ProductRepository $productRepository
     * @param Helpers $helpers
     * @param ProductService $productService
     */
    public function __construct(
        ProductRepository $productRepository,
        Helpers $helpers,
        ProductService $productService)
    {
        parent::__construct($helpers);

        $this->productRepository = $productRepository;
        $this->productService = $productService;
    }

    /**
     * get User Ip.
     *
     * @Rest\Get("/api/user/ip")
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="User")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object Products",
     * )
     *
     * @return array
     * @throws DBALException
     */
    public function getUserIpAction(Request $request)
    {
        return [
            'ip' => $request->getClientIp()
        ];
    }

    /**
     * get Products extra fields.
     *
     * @Rest\Get("/api/products/extra_fields")
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Products")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object Products",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws DBALException
     * @throws \Exception
     */
    public function fetchProductExtraFieldsAction()
    {
        $allExtrasKey = $this->getProductRepository()
            ->fetchAllExtrasFieldsWithCache();
        $view = $this->createSuccessResponse($allExtrasKey);
        $view->getResponse()->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }

    /**
     * get Extra Fields Facet filters.
     *
     * @Rest\Get("/api/products/extra_fields/facet_filters/{uniqIdentificationQuery}")
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Products")
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
    public function fetchFacetProductExtraFieldsAction($uniqIdentificationQuery)
    {
        $extraFields = $this->getProductService()
            ->facetFilters($uniqIdentificationQuery);
        $view = $this->createSuccessResponse($extraFields);
        $view
            ->getResponse()
            ->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }


    /**
     * get Products.
     *
     * @Rest\Get("/api/products")
     *
     * @Rest\QueryParam(map=true, name="extra_array", requirements=@ExtraFields, nullable=true, strict=true, default="0", description="array of extra keys and values")
     * @Rest\QueryParam(map=true, name="shop_ids", nullable=true, strict=true, requirements="\d+", default="0", description="array of shop ids")
     * @Rest\QueryParam(map=true, name="category_ids", nullable=true, strict=true, requirements="\d+", default="0", description="array of category ids")
     * @Rest\QueryParam(map=true, name="brand_ids", nullable=true, strict=true, requirements="\d+", default="0", description="array of brand ids")
     * @Rest\QueryParam(
     *     name="category_word",
     *     strict=true,
     *     requirements=@SearchQueryParam,
     *     nullable=true,
     *     description="Search Categories by name like paetial of word and then search product by categories")
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
     *                      @SWG\Property(property="categoryIds", type="string"),
     *                      @SWG\Property(property="shopId", type="integer"),
     *                      @SWG\Property(property="shop", type="string"),
     *                  )
     *             ),
     *             @SWG\Property(property="count", type="integer")
     *         }
     *     )
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws DBALException
     * @throws ValidatorException
     */
    public function getProductsAction(ParamFetcher $paramFetcher, Request $request)
    {
        $searchProductCollection = $this->getProductService()
            ->searchProductsByFilter($paramFetcher);
        $view = $this->createSuccessResponse(
            $searchProductCollection,
            [SearchProductCollection::GROUP_GET]
        );
        $view->getResponse()
            ->setPublic()
            ->setMaxAge(600);

        return $view;
    }

    /**
     * get Product data by id.
     *
     * @Rest\Get("/api/product/{id}", requirements={"id"="\d+"})
     * @View(serializerGroups={Product::SERIALIZED_GROUP_LIST}, statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Products")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json object Product with relation items",
     *     @SWG\Schema(
     *         type="object",
     *         properties={
     *             @SWG\Property(property="product",
     *                  @Model(type=Product::class, groups={Product::SERIALIZED_GROUP_LIST})),
     *             @SWG\Property(
     *                  property="relatedItems",
     *                  type="array",
     *                  description="related products by price, band name and categories names",
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
     *             )
     *         }
     *     )
     * )
     *
     * @param Product $product
     * @param ParamFetcher $paramFetcher
     * @param Request $request
     *
     * @return ProductCollection
     * @throws DBALException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getProductByIdAction(
        Product $product
    )
    {
        return $this->getProductService()
            ->getProductById($product);
    }

    /**
     * get Products by ids.
     *
     * @Rest\Get("/api/products/by/ids")
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
     * @return \FOS\RestBundle\View\View
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function getProductByIdsAction(ParamFetcher $paramFetcher)
    {
        $collection = $this->getProductRepository()->getProductByIds($paramFetcher);
        $count = $this->getProductRepository()->getProductByIds($paramFetcher, true);
        $productsCollection = new ProductsCollection($collection, $count);
        $view = $this->createSuccessResponse($productsCollection, [Product::SERIALIZED_GROUP_LIST]);
        $view->getResponse()->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }

    /**
     * get Top Products by ip.
     *
     * @Rest\Get("/api/top/products/by/ip")
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Products")
     *
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     *
     * @param ParamFetcher $paramFetcher
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
     *                      @SWG\Property(property="product_id", type="integer"),
     *                      @SWG\Property(property="number_of_entries", type="integer"),
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
     *                      @SWG\Property(property="categoryIds", type="string"),
     *                      @SWG\Property(property="shopId", type="integer"),
     *                      @SWG\Property(property="shop", type="string"),
     *                  )
     *             ),
     *             @SWG\Property(property="count", type="integer")
     *         }
     *     )
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getProductByIpAction(ParamFetcher $paramFetcher)
    {
        $searchProductCollection = $this->getProductService()
            ->getProductByIp($paramFetcher);
        $view = $this->createSuccessResponse($searchProductCollection, [], false);
        $view->getResponse()->setMaxAge(180);

        return $view;
    }

    /**
     * get Top Products.
     *
     * @Rest\Get("/api/top/products")
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Products")
     *
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     *
     * @param ParamFetcher $paramFetcher
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
     *                      @SWG\Property(property="product_id", type="integer"),
     *                      @SWG\Property(property="number_of_entries", type="integer"),
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
     *                      @SWG\Property(property="categoryIds", type="string"),
     *                      @SWG\Property(property="shopId", type="integer"),
     *                      @SWG\Property(property="shop", type="string"),
     *                  )
     *             ),
     *             @SWG\Property(property="count", type="integer")
     *         }
     *     )
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getMostPopularProductAction(ParamFetcher $paramFetcher)
    {
        $searchProductCollection = $this->getProductService()
            ->getMostPopularProducts($paramFetcher);
        $view = $this->createSuccessResponse($searchProductCollection, [], false);
        $view->getResponse()->setMaxAge(180);

        return $view;
    }

    /**
     * @return ProductRepository
     */
    protected function getProductRepository(): ProductRepository
    {
        return $this->productRepository;
    }

    /**
     * @return ProductService
     */
    protected function getProductService(): ProductService
    {
        return $this->productService;
    }
}