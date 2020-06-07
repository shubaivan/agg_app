<?php

namespace App\Services\Models;

use App\Cache\TagAwareQueryResultCacheProduct;
use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Collection\BrandsCollection;
use App\Entity\Collection\CategoriesCollection;
use App\Entity\Collection\Search\SearchCategoriesCollection;
use App\Entity\Product;
use App\Entity\Shop;
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
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function matchCategoryWithSubFetcher(ParamFetcher $paramFetcher)
    {
        $parameterBag = new ParameterBag($paramFetcher->all());

        return  $this->matchCategoryWithSub(0 , $parameterBag, true);
    }

    /**
     * @param Product $product
     * @param Category $category
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function analysisProductByMainCategoryManual(
        Product $product,
        Category $category
    )
    {
        return $this->analysisProductByMainCategory(
            $product, $category->getCategoryName(), true
        );
    }

    /**
     * @param int $productId
     * @param ParameterBag $parameterBag
     * @param bool $explain
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function matchCategoryWithSub(int $productId, ParameterBag $parameterBag, bool $explain = false) {
        $depth[] = $parameterBag->get( self::MAIN_SEARCH );

        if ($parameterBag->get( self::SUB_MAIN_SEARCH)) {
            $depth[] = $parameterBag->get(self::SUB_MAIN_SEARCH);
        }

        if ($parameterBag->get(self::SUB_SUB_MAIN_SEARCH)) {
            $depth[] = $parameterBag->get(self::SUB_SUB_MAIN_SEARCH);
        }

        return $this->getCategoryRepository()->matchCategoryWithSub(
            $productId, $parameterBag, count($depth), $explain
        );
    }

    /**
     * @param Product $product
     * @return array|mixed[]|void
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handleAnalysisProductByMainCategory(Product $product)
    {
        $mainSubCategoryIds = $this->getCategoryRepository()
            ->getMainSubCategoryIds();
        $mainCategoryWords = [];
        foreach ($mainSubCategoryIds as $main) {
            if (isset($main['category_name'])) {
                $mainCategoryWords[] = $main['category_name'];
            }
        }
        if (!count($mainCategoryWords)) {
            return [];
        }

        $mainCategoryWordsString = implode(',', $mainCategoryWords);
        $resultAnalysis = $this->analysisProductByMainCategory(
            $product, $mainCategoryWordsString
        );
        if (!count($resultAnalysis)) {
            return [];
        }

        $mainArrayIds = [];
        foreach ($resultAnalysis as $categoryIds) {
            if (isset($categoryIds['id'])) {
                $mainArrayIds[] = $categoryIds['id'];
            }
            if (isset($categoryIds['sub_ctegory_id'])) {
                $mainArrayIds[] = $categoryIds['sub_ctegory_id'];
            }
            if (isset($categoryIds['sub_sub_category_id'])) {
                $mainArrayIds[] = $categoryIds['sub_sub_category_id'];
            }
        }

        $mainArrayIds = array_unique($mainArrayIds);
        $this->addCategoryToProductByIds($mainArrayIds, $product);

        return $resultAnalysis;
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
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\DBALException
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
        $matchData = strip_tags($matchData);
        if (preg_match_all('/[a-zA-Z éäöåÉÄÖÅ]+/',$matchData,$matches)) {
            $matchData = array_shift($matches);
            if (is_array($matchData) && count($matchData)) {
                $arrayFilter = array_filter($matchData, function ($v) {
                    if (strlen($v) > 3) {
                        return true;
                    }
                });
                $resultData = implode(',', $arrayFilter);
                $parameterBag->set(self::SUB_MAIN_SEARCH, $resultData);
                $parameterBag->set(self::SUB_SUB_MAIN_SEARCH, $resultData);

                $matchCategoryWithSub = $this->matchCategoryWithSub($product->getId(), $parameterBag, $explain);

                return $matchCategoryWithSub;
            }
        }

        return [];
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