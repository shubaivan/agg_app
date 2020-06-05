<?php

namespace App\Services\Models;

use App\Cache\TagAwareQueryResultCacheProduct;
use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Collection\BrandsCollection;
use App\Entity\Collection\CategoriesCollection;
use App\Entity\Collection\Search\SearchCategoriesCollection;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Services\ObjectsHandler;
use Doctrine\DBAL\Cache\CacheException;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\ParameterBag;

class CategoryService
{
    const MAIN_SEARCH = 'main_search';
    const SUB_MAIN_SEARCH = 'sub_main_search';
    const SUB_SUB_MAIN_SEARCH = 'sub_sub_main_search';
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
     * @return CategoriesCollection
     * @throws CacheException
     */
    public function getCustomCategories(ParamFetcher $paramFetcher)
    {
        $collection = $this->getCategoryRepository()->getCustomCategories($paramFetcher);
        $count = 0;
        return (new CategoriesCollection($collection, $count));
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return array
     * @throws CacheException
     */
    public function matchCategoryWithSubFetcher(ParamFetcher $paramFetcher)
    {
        $parameterBag = new ParameterBag($paramFetcher->all());

        return  $this->matchCategoryWithSub($parameterBag, true);
    }

    /**
     * @param Product $product
     * @param Category $category
     * @return mixed[]
     * @throws CacheException
     */
    public function analysisProductByMainCategoryManual(
        Product $product,
        Category $category
    )
    {
        return $this->analysisProductBySubMainCategory(
            $product, $category->getCategoryName(), true
        );
    }

    /**
     * @param ParameterBag $parameterBag
     * @param bool $explain
     * @return mixed[]
     * @throws CacheException
     */
    public function matchCategoryWithSub(ParameterBag $parameterBag, bool $explain = false) {
        $depth[] = $parameterBag->get( self::MAIN_SEARCH );

        if ($parameterBag->get( self::SUB_MAIN_SEARCH)) {
            $depth[] = $parameterBag->get(self::SUB_MAIN_SEARCH);
        }

        if ($parameterBag->get(self::SUB_SUB_MAIN_SEARCH)) {
            $depth[] = $parameterBag->get(self::SUB_SUB_MAIN_SEARCH);
        }

        return $this->getCategoryRepository()->matchCategoryWithSub(
            $parameterBag, count($depth), $explain
        );
    }

    /**
     * @param Product $product
     * @return mixed[]
     * @throws CacheException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function analysisProductByMainBarnCategory(Product $product)
    {
        $analysisProductBySubMainCategory = $this->analysisProductBySubMainCategory(
            $product, 'Barn'
        );
        $analysisProductByMainCategory = $this->analysisProductByMainCategory(
            $product, 'Barn'
        );
        $resultAnalysis = array_merge($analysisProductByMainCategory, $analysisProductBySubMainCategory);
        $mainIds = [];
        $subIds = [];
        $subSubIds = [];
        foreach ($resultAnalysis as $categoryIds) {
            if (isset($categoryIds['id'])) {
                $mainIds[] = $categoryIds['id'];
            }
            if (isset($categoryIds['sub_ctegory_id'])) {
                $subIds[] = $categoryIds['sub_ctegory_id'];
            }
            if (isset($categoryIds['sub_sub_category_id'])) {
                $subSubIds[] = $categoryIds['sub_sub_category_id'];
            }
        }
        $mainIds = array_unique($mainIds);
        $subIds = array_unique($subIds);
        $subSubIds = array_unique($subSubIds);
        $mainArrayIds = array_merge($mainIds, $subIds, $subSubIds);
        $this->addCategoryToProductByIds($mainArrayIds, $product);

        return $analysisProductByMainCategory;
    }

    /**
     * @param array $ids
     * @param Product $product
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addCategoryToProductByIds(array $ids, Product $product)
    {
        foreach ($ids as $id )
        {
            $category = $this->getCategoryRepository()->findOneBy(['id' => $id]);
            if ($category) {
                $product->addCategoryRelation($category);
            }
        }
    }

    /**
     * @param Product $product
     * @param string $mainCategoryKeyWord
     * @param bool $explain
     * @return mixed[]
     * @throws CacheException
     */
    public function analysisProductBySubMainCategory(
        Product $product,
        string $mainCategoryKeyWord,
        bool $explain = false
    )
    {
        $parameterBag = new ParameterBag();
        $parameterBag->set(CategoryRepository::STRICT, true);
        $parameterBag->set(self::MAIN_SEARCH, $mainCategoryKeyWord);

        $matchData = preg_replace('!\s+!', ',', $product->getName() . ', ' . $product->getDescription());

        $parameterBag->set(self::SUB_MAIN_SEARCH, $matchData);

        $matchCategoryWithSub = $this->matchCategoryWithSub($parameterBag, $explain);

        return $matchCategoryWithSub;
    }

    /**
     * @param Product $product
     * @param string $mainCategoryKeyWord
     * @param bool $explain
     * @return mixed[]
     * @throws CacheException
     */
    public function analysisProductByMainCategory(
        Product $product,
        string $mainCategoryKeyWord,
        bool $explain = false
    )
    {
        $parameterBag = new ParameterBag();
        $parameterBag->set(CategoryRepository::STRICT, true);
        $parameterBag->set(self::MAIN_SEARCH, $mainCategoryKeyWord);

        $matchData = preg_replace('!\s+!', ',', $product->getName() . ', ' . $product->getDescription());

        $parameterBag->set(self::SUB_MAIN_SEARCH, $matchData);
        $parameterBag->set(self::SUB_SUB_MAIN_SEARCH, $matchData);

        $matchCategoryWithSub = $this->matchCategoryWithSub($parameterBag, $explain);

        return $matchCategoryWithSub;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return SearchCategoriesCollection
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

            return (new SearchCategoriesCollection($strictCategoriesCollection, $countStrict));
        }
        $parameterBag->remove('strict');
        $count = $this->getCategoryRepository()
            ->fullTextSearchByParameterBag($parameterBag, true);
        $categoriesCollection = $this->getCategoryRepository()
            ->fullTextSearchByParameterBag($parameterBag);

        return (new SearchCategoriesCollection($categoriesCollection, $count));
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
     * @return SearchCategoriesCollection
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
        $pregSplitCategoryQuery = preg_split('/&&/', $categoryQuery[0]);
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

        return new SearchCategoriesCollection($facetFilters, $facetFiltersCount);
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
     * @param ParamFetcher $paramFetcher
     * @return Category[]|CategoriesCollection|int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCategoryByIds(ParamFetcher $paramFetcher)
    {
        $collection = $this->getCategoryRepository()
            ->getCategoryByIds($paramFetcher);
        $count = $this->getCategoryRepository()
            ->getCategoryByIds($paramFetcher, true);
        $collection = new CategoriesCollection($collection, $count);

        return $collection;
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