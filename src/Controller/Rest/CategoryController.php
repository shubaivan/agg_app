<?php

namespace App\Controller\Rest;

use App\Entity\Collection\CategoriesCollection;
use App\Repository\CategoryRepository;
use App\Entity\Category;
use App\Services\Helpers;
use App\Services\Models\CategoryService;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use App\Validation\Constraints\SearchQueryParam;

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
     * @return CategoriesCollection
     * @throws DBALException
     */
    public function getCategoriesAction(ParamFetcher $paramFetcher)
    {
        return $this->getCategoryService()->getCategoriesByFilter($paramFetcher);
    }

    /**
     * get Category by id.
     *
     * @Rest\Get("/api/category/{id}", requirements={"id"="\d+"})
     *
     * @View(serializerGroups={Category::SERIALIZED_GROUP_LIST}, statusCode=Response::HTTP_OK)
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
     * @return Category
     */
    public function getCategoryByIdAction(
        Category $category
    )
    {
        return $category;
    }

    /**
     * @return CategoryService
     */
    private function getCategoryService(): CategoryService
    {
        return $this->categoryService;
    }
}