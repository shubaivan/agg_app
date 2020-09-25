<?php

namespace App\Controller\Rest;

use App\Entity\Category;
use App\Entity\Product;
use App\Services\Helpers;
use App\Services\Models\CategoryService;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use App\Validation\Constraints\SearchQueryParam;
use App\Entity\Collection\CategoriesCollection;

class CategoryController extends AbstractRestController
{
    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * CategoryController constructor.
     * @param CategoryService $categoryService
     * @param Helpers $helpers
     */
    public function __construct(CategoryService $categoryService, Helpers $helpers)
    {
        parent::__construct($helpers);
        $this->categoryService = $categoryService;
    }

    /**
     * analysis Product by category.
     *
     * @Rest\Get("/api/main_category/{c_id}/product/{p_id}")
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View()
     *
     * @SWG\Tag(name="Category")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object Categories"
     * )
     *
     * @return array
     *
     * @ParamConverter("category", class="App\Entity\Category", options={"id" = "c_id"})
     * @ParamConverter("product", class="App\Entity\Product", options={"id" = "p_id"})
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAnalysisProductByCategory(
        Category $category,
        Product $product
    )
    {
        return $this->getCategoryService()->analysisProductByMainCategoryManual(
            $product,
            $category
        );
    }

    /**
     * get runk Category.
     *
     * @Rest\Get("/api/categories/runk")
     *
     * @Rest\QueryParam(
     *     name=CategoryService::MAIN_SEARCH,
     *     strict=true,
     *     requirements=@SearchQueryParam,
     *     nullable=false,
     *     description="Search by each sentence/world separatly delimetery which eqaul ',', with `or` condition by keywords fields")
     *
     * @Rest\QueryParam(
     *     name=CategoryService::SUB_MAIN_SEARCH,
     *     strict=true,
     *     requirements=@SearchQueryParam,
     *     nullable=true,
     *     description="Search by each sentence/world separatly delimetery which eqaul ',', with `or` condition by keywords fields")
     *
     * @Rest\QueryParam(
     *     name=CategoryService::SUB_SUB_MAIN_SEARCH,
     *     strict=true,
     *     requirements=@SearchQueryParam,
     *     nullable=true,
     *     description="Search by each sentence/world separatly delimetery which eqaul ',', with `or` condition by keywords fields")
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View()
     *
     * @SWG\Tag(name="Category")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object Categories"
     * )
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getMatchCategoryWithSub(ParamFetcher $paramFetcher)
    {
        $collection = $this->getCategoryService()->matchCategoryWithSubFetcher($paramFetcher);

        return $collection;
    }

    /**
     * get custom Category.
     *
     * @Rest\Get("/api/categories/custom")
     *
     * @Rest\QueryParam(
     *     name="search",
     *     strict=true,
     *     requirements=@SearchQueryParam,
     *     nullable=true,
     *     description="Search by each sentence/world separatly delimetery which eqaul ',', with `or` condition by category_name fields")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View(serializerGroups={Category::SERIALIZED_GROUP_RELATIONS_LIST}, statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Category")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object Categories",
     *     @SWG\Schema(ref=@Model(type=CategoriesCollection::class, groups={Category::SERIALIZED_GROUP_LIST}))
     * )
     *
     * @return CategoriesCollection
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCustomCategoriesAction(ParamFetcher $paramFetcher)
    {
        $collection = $this->getCategoryService()->getCustomCategories($paramFetcher);

        return $collection;
    }

    /**
     * get custom Category.
     *
     * @Rest\Get("/api/categories/hot")
     *
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by position, created_at, category_name, id", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View(serializerGroups={Category::SERIALIZED_GROUP_RELATIONS_LIST}, statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Category")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object Categories",
     *     @SWG\Schema(ref=@Model(type=CategoriesCollection::class, groups={Category::SERIALIZED_GROUP_LIST}))
     * )
     *
     * @return CategoriesCollection
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getHotCategoriesAction(ParamFetcher $paramFetcher)
    {
        $collection = $this->getCategoryService()->getHotCategories($paramFetcher);

        return $collection;
    }


    /**
     * get Category.
     *
     * @Rest\Get("/api/categories")
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
     * @SWG\Tag(name="Category")
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
    public function getCategoriesAction(ParamFetcher $paramFetcher)
    {
        $categoriesCollection = $this->getCategoryService()->getCategoriesByFilter($paramFetcher);
        $view = $this->createSuccessResponse($categoriesCollection);
        $view
            ->getResponse()
            ->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }

    /**
     * get Category by slug.
     *
     * @Rest\Get("/api/category/{slug}")
     *
     * @SWG\Tag(name="Category")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json object with relation items",
     *     @Model(type=Category::class, groups={Category::SERIALIZED_GROUP_LIST}))
     *     )
     * )
     *
     * @param Category $category
     *
     * @return \FOS\RestBundle\View\View
     *
     * @throws \Exception
     */
    public function getCategoryByIdAction(
        Category $category
    )
    {
        $view = $this->createSuccessResponse($category, [Category::SERIALIZED_GROUP_LIST]);
        $view
            ->getResponse()
            ->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }

    /**
     * get Category Facet filters.
     *
     * @Rest\Get("/api/category/facet_filters/{uniqIdentificationQuery}")
     *
     * @Rest\QueryParam(
     *     name="search",
     *     strict=true,
     *     requirements=@SearchQueryParam,
     *     nullable=true,
     *     description="Search by each sentence/world separatly delimetery which eqaul ',', with `or` condition by category_name fields")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Category")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object"
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws \Exception
     */
    public function getCategoriesFacetFiltersAction(ParamFetcher $paramFetcher, $uniqIdentificationQuery)
    {
        $collection = $this->getCategoryService()
            ->facetFilters($uniqIdentificationQuery, $paramFetcher);
        $view = $this->createSuccessResponse($collection);
        $view
            ->getResponse()
            ->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }

    /**
     * get Categories by ids.
     *
     * @Rest\Get("/api/categories/by/ids")
     *
     * @SWG\Tag(name="Category")
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
     *     @SWG\Schema(ref=@Model(type=CategoriesCollection::class, groups={Category::SERIALIZED_GROUP_LIST}))
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function getCategoriesByIdsAction(ParamFetcher $paramFetcher)
    {
        $productsCollection = $this->getCategoryService()
            ->getCategoryByIds($paramFetcher);
        $view = $this->createSuccessResponse(
            $productsCollection, [Category::SERIALIZED_GROUP_LIST]
        );
        $view->getResponse()->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }


    /**
     * @return CategoryService
     */
    private function getCategoryService(): CategoryService
    {
        return $this->categoryService;
    }
}