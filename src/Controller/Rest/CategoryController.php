<?php

namespace App\Controller\Rest;

use App\Entity\Collection\CategoriesCollection;
use App\Repository\CategoryRepository;
use App\Entity\Category;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class CategoryController extends AbstractRestController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * CategoryController constructor.
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * get Category.
     *
     * @Rest\Get("/api/categories")
     *
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View(serializerGroups={Category::SERIALIZED_GROUP_LIST}, statusCode=Response::HTTP_OK)
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
    public function getCategoriesAction(ParamFetcher $paramFetcher)
    {
        $collection = $this->getCategoryRepository()->getEntityList($paramFetcher);
        $count = $this->getCategoryRepository()->getEntityList($paramFetcher, true);
        return (new CategoriesCollection($collection, $count));
    }

    /**
     * @return CategoryRepository
     */
    public function getCategoryRepository(): CategoryRepository
    {
        return $this->categoryRepository;
    }
}