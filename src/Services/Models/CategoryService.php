<?php

namespace App\Services\Models;

use App\Cache\TagAwareQueryResultCacheProduct;
use App\Entity\Category;
use App\Entity\Collection\BrandsCollection;
use App\Entity\Collection\CategoriesCollection;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Services\ObjectsHandler;
use Doctrine\DBAL\Cache\CacheException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryService
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var ObjectsHandler
     */
    private $objecHandler;

    /**
     * @var TagAwareQueryResultCacheProduct
     */
    private $tagAwareQueryResultCacheProduct;

    /**
     * CategoryService constructor.
     * @param CategoryRepository $categoryRepository
     * @param ObjectsHandler $objecHandler
     * @param TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ObjectsHandler $objecHandler,
        TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct
    )
    {
        $this->tagAwareQueryResultCacheProduct = $tagAwareQueryResultCacheProduct;
        $this->categoryRepository = $categoryRepository;
        $this->objecHandler = $objecHandler;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return CategoriesCollection
     * @throws CacheException
     */
    public function getCategoriesByFilter(ParamFetcher $paramFetcher, $count = false)
    {
        $parameterBag = new ParameterBag($paramFetcher->all());
        $parameterBag->set('strict', true);
        $countStrict = $this->getCategoryRepository()
            ->fullTextSearchByParameterBag($parameterBag, true);
        if ($countStrict > 0) {
            $strictCategoriesCollection = $this->getCategoryRepository()
                ->fullTextSearchByParameterBag($parameterBag);

            return (new CategoriesCollection($strictCategoriesCollection, $countStrict));
        }
        $parameterBag->remove('strict');
        $count = $this->getCategoryRepository()
            ->fullTextSearchByParameterBag($parameterBag, true);
        $categoriesCollection = $this->getCategoryRepository()
            ->fullTextSearchByParameterBag($parameterBag);

        return (new CategoriesCollection($categoriesCollection, $count));
    }

    /**
     * @param Product $product
     * @return array|bool
     * @throws \App\Exception\ValidatorException
     */
    public function createCategoriesFromProduct(Product $product)
    {
        $stringCategories = $product->getCategory();
        if (strlen($stringCategories) < 1) {
            return false;
//            throw new BadRequestHttpException('product id:' . $product->getId() . ' category is empty');
        }
        $matchAll = preg_match_all('/[-]/', $stringCategories, $matches);
        $arrayCategories = [];
        if ($matchAll > 0) {
            $arrayCategories = array_unique(explode(' - ', $stringCategories));
        } else {
            array_push($arrayCategories, $stringCategories);
        }

        return $this->createArrayModelCategory($arrayCategories, $product);
    }

    /**
     * @param string $uniqIdentificationQuery
     * @param ParamFetcher $paramFetcher
     * @return CategoriesCollection
     * @throws \Exception
     */
    public function facetFilters(
        string $uniqIdentificationQuery,
        ParamFetcher $paramFetcher
    )
    {
        $facetQueries = $this->getTagAwareQueryResultCacheProduct()
            ->fetch($uniqIdentificationQuery);

        if (!is_array($facetQueries)) {
            throw new \Exception('redis key not present');
        }

        if (count($facetQueries) < 1) {
            throw new \Exception('redis key is empty');
        }

        if (!isset($facetQueries[ProductRepository::FACET_CATEGORY_QUERY_KEY])) {
            throw new \Exception('facet key ' . ProductRepository::FACET_CATEGORY_QUERY_KEY . ' not present');
        }

        $categoryQuery = $facetQueries[ProductRepository::FACET_CATEGORY_QUERY_KEY];
        $pregSplitCategoryQuery = preg_split('/&/', $categoryQuery[0]);
        $query = preg_replace('/query=/', '', $pregSplitCategoryQuery[0]);
        $params = unserialize(preg_replace('/params=/', '', $pregSplitCategoryQuery[1]));
        $types = unserialize(preg_replace('/types=/', '', $pregSplitCategoryQuery[2]));

        $facetFilters = $this->getCategoryRepository()
            ->facetFilters(
                (new ParameterBag($paramFetcher->all())),
                $query,
                $params,
                $types
            );

        $facetFiltersCountQuery = preg_replace(
            '/SELECT(.|\n*)+FROM/',
            'SELECT COUNT(DISTINCT category_alias.id) FROM ',
            $query
        );

        $facetFiltersCount = $this->getCategoryRepository()
            ->facetFilters(
                (new ParameterBag($paramFetcher->all())),
                $facetFiltersCountQuery,
                $params,
                $types,
                true
            );

        return new CategoriesCollection($facetFilters, $facetFiltersCount);
    }

    /**
     * @param array $arrayCategories
     * @param Product $product
     * @return array
     * @throws \App\Exception\ValidatorException
     */
    private function createArrayModelCategory(array $arrayCategories, Product $product)
    {
        $arrayModelsCategory = [];
        foreach ($arrayCategories as $category) {
            $categoryModel = $this->matchExistCategory($category);
            if (!($categoryModel instanceof Category)) {
                $categoryModel = new Category();
                $categoryModel
                    ->setCategoryName($category);

                $this->getObjecHandler()
                    ->validateEntity($categoryModel, [Category::SERIALIZED_GROUP_CREATE]);
            }
            $product->addCategoryRelation($categoryModel);
            array_push($arrayModelsCategory, $categoryModel);
        }

        return $arrayModelsCategory;
    }

    /**
     * @param string $name
     * @return Category|object|null
     */
    private function matchExistCategory(string $name)
    {
        return $this->getCategoryRepository()
            ->findOneBy(['categoryName' => $name]);
    }

    /**
     * @return ObjectsHandler
     */
    private function getObjecHandler(): ObjectsHandler
    {
        return $this->objecHandler;
    }

    /**
     * @return CategoryRepository
     */
    private function getCategoryRepository(): CategoryRepository
    {
        return $this->categoryRepository;
    }

    /**
     * @return TagAwareQueryResultCacheProduct
     */
    private function getTagAwareQueryResultCacheProduct(): TagAwareQueryResultCacheProduct
    {
        return $this->tagAwareQueryResultCacheProduct;
    }
}