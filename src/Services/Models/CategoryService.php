<?php

namespace App\Services\Models;

use App\Cache\TagAwareQueryResultCacheProduct;
use App\Entity\AdminConfiguration;
use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\CategoryRelations;
use App\Entity\Collection\BrandsCollection;
use App\Entity\Collection\CategoriesCollection;
use App\Entity\Collection\Search\SearchCategoriesCollection;
use App\Entity\Product;
use App\Entity\Shop;
use App\Exception\GlobalMatchException;
use App\Exception\GlobalMatchExceptionBrand;
use App\Repository\CategoryConfigurationsRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Services\Helpers;
use App\Services\ObjectsHandler;
use App\Util\RedisHelper;
use Doctrine\DBAL\Cache\CacheException;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\ParameterBag;

class CategoryService extends AbstractModel
{
    const MAIN_SEARCH = 'main_search';
    const SUB_MAIN_SEARCH = 'sub_main_search';
    const SUB_SUB_MAIN_SEARCH = 'sub_sub_main_search';
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var CategoryConfigurationsRepository
     */
    private $categoryConfigurationsRepository;

    /**
     * @var ObjectsHandler
     */
    private $objecHandler;

    /**
     * @var TagAwareQueryResultCacheProduct
     */
    private $tagAwareQueryResultCacheProduct;

    /**
     * @var Helpers
     */
    private $helper;

    /**
     * @var RedisHelper
     */
    private $redisHelper;

    /**
     * CategoryService constructor.
     * @param CategoryRepository $categoryRepository
     * @param ObjectsHandler $objecHandler
     * @param TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct
     * @param Helpers $helper
     * @param RedisHelper $redisHelper
     * @param CategoryConfigurationsRepository $categoryConfigurationsRepository
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ObjectsHandler $objecHandler,
        TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct,
        Helpers $helper,
        RedisHelper $redisHelper,
        CategoryConfigurationsRepository $categoryConfigurationsRepository
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->objecHandler = $objecHandler;
        $this->tagAwareQueryResultCacheProduct = $tagAwareQueryResultCacheProduct;
        $this->helper = $helper;
        $this->redisHelper = $redisHelper;
        $this->categoryConfigurationsRepository = $categoryConfigurationsRepository;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return CategoriesCollection
     * @throws CacheException
     */
    public function getHotCategories(ParamFetcher $paramFetcher)
    {
        $collection = $this->getCategoryRepository()->getEntityList($paramFetcher);
        $count = $this->getCategoryRepository()->getEntityList($paramFetcher, true);
        return (new CategoriesCollection($collection, $count));
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return CategoriesCollection
     * @throws CacheException
     */
    public function getCustomCategories(ParamFetcher $paramFetcher)
    {
        $collection = $this->getCategoryRepository()->getCustomCategories($paramFetcher);
        $count = count($collection);
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

        return  $this->matchCategoryWithSub(null , $parameterBag, true);
    }

    /**
     * @param Product $product
     * @param Category $category
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function analysisProductByMainCategoryManual(
        Product $product,
        Category $category
    )
    {
        if (!$category->getCategoryConfigurations()) {
            throw new \Exception('category don\'t have configuration modle');
        }

        $mainCategoryWords = [];
        $mainCategoryWords['categories'][$category->getCategoryName()]['positive'] = $category->getCategoryConfigurations()->getKeyWords();
        $negativeKeyWords = $category->getCategoryConfigurations()->getNegativeKeyWords();
        if (strlen($negativeKeyWords)) {
            $negativeKeyWords = implode(',', array_map(function ($v) {return '!' . $v;}, explode(', ', $negativeKeyWords)));
        } else {
            $negativeKeyWords = null;
        }

        $mainCategoryWords['categories'][$category->getCategoryName()]['negative'] = $negativeKeyWords;
        if ($negativeKeyWords) {
            $mainCategoryWords['common'][] = '(' . $category->getCategoryConfigurations()->getKeyWords() . ', ' . $negativeKeyWords . ')';
        } else {
            $mainCategoryWords['common'][] = $category->getCategoryConfigurations()->getKeyWords();
        }

        $isMatchPlainCategories = $this->getCategoryRepository()->isMatchPlainCategoriesString(
            $product->getCategory(),
            $mainCategoryWords,
            true
        );

        $analysisProductByMainCategory = $this->analysisProductByMainCategory(
            $product,
            $mainCategoryWords,
            true
        );

        if (isset($isMatchPlainCategories['ts_headline_result'])) {
            $analysisProductByMainCategory['category_ts_headline_result'] = $isMatchPlainCategories['ts_headline_result'];
        }

        return $analysisProductByMainCategory;
    }

    /**
     * @param Product|null $product
     * @param ParameterBag $parameterBag
     * @param bool $explain
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function matchCategoryWithSub(?Product $product = null, ParameterBag $parameterBag, bool $explain = false) {
        $depth[] = $parameterBag->get( self::MAIN_SEARCH );

        if ($parameterBag->get( self::SUB_MAIN_SEARCH)) {
            $depth[] = $parameterBag->get(self::SUB_MAIN_SEARCH);
        }

        if ($parameterBag->get(self::SUB_SUB_MAIN_SEARCH)) {
            $depth[] = $parameterBag->get(self::SUB_SUB_MAIN_SEARCH);
        }

        return $this->getCategoryRepository()->matchCategoryWithSub(
            $product, $parameterBag, count($depth), $explain
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
        $mainCategoryWords = $this->getCategoryRepository()
            ->getMainSubCategoryIds();

        if (!count($mainCategoryWords)) {
            return [];
        }

        $resultAnalysis = $this->analysisProductByMainCategory(
            $product, $mainCategoryWords
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
     * @param array $mainCategoryKeyWord
     * @param bool $explain
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function analysisProductByMainCategory(
        Product $product,
        array $mainCategoryKeyWord,
        bool $explain = false
    )
    {
        $result = [];
        $parameterBag = new ParameterBag();
        $parameterBag->set(CategoryRepository::STRICT, true);
        $parameterBag->set(self::MAIN_SEARCH, $mainCategoryKeyWord);
        $matchCategoryMain = $this->matchCategoryWithSub(
            $product, $parameterBag, $explain
        );
        if (!$matchCategoryMain) {
            return [];
        }

        $extras = $product->getExtras();
        if (isset($extras[Product::SIZE])) {
            $sizeCategoriesIds = $this->categoryConfigurationsRepository
                ->matchSizeCategories($extras[Product::SIZE], $matchCategoryMain);
            if (count($sizeCategoriesIds)) {
                $matchCategoryMain = array_merge($sizeCategoriesIds, $matchCategoryMain);
            }
        }
        
        $result = array_merge($result, $matchCategoryMain);
        $prepareDataForGINSearch = $this->prepareProductDataForMatching(
            $product->getName() . ', ' . $product->getDescription()
        );
        if ($prepareDataForGINSearch) {
            $resultData = $prepareDataForGINSearch;
            $parameterBag->set(self::SUB_MAIN_SEARCH, $resultData);
            $matchCategoryWithoutSub = $this->matchCategoryWithSub($product, $parameterBag, $explain);

            if (!$matchCategoryWithoutSub) {
                return  $result;
            }
            $result = array_merge($result, $matchCategoryWithoutSub);
            $parameterBag->set(self::SUB_SUB_MAIN_SEARCH, $resultData);
            $matchCategoryWithSub = $this->matchCategoryWithSub($product, $parameterBag, $explain);

            if (!$matchCategoryWithSub) {
                return  $result;
            }
            $result = array_merge($result, $matchCategoryWithSub);

            return $result;
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
            if ($categoryModel->getSubCategoryRelations()->count()) {
                foreach ($categoryModel->getSubCategoryRelations()->getIterator() as $categoryRelation) {
                    /** @var $categoryRelation CategoryRelations */
                    if ($categoryRelation->getMainCategory()) {
                        $product->addCategoryRelation($categoryRelation->getMainCategory());
                    }
                }
            }
            $product->addCategoryRelation($categoryModel);
            array_push($arrayModelsCategory, $categoryModel);
        }

        return $arrayModelsCategory;
    }

    public function matchCustomerExcludeRules(Product $product)
    {

    }

    /**
     * @param Product $product
     * @return bool
     * @throws GlobalMatchException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function matchGlobalNegativeKeyWords(Product $product)
    {
        $productComparingData = $product->getName() . ', ' . $product->getDescription();
        if (!strlen($productComparingData)) {
            return false;
        }
        $prepareDataForGINSearch = $this->prepareProductDataForMatching($productComparingData, false, 3);
        if (!strlen($prepareDataForGINSearch)) {
            return false;
        }
        $matchGlobalNegativeKeyWords = $this->getCategoryRepository()->matchGlobalNegativeKeyWords(
            $prepareDataForGINSearch,
            AdminConfiguration::GLOBAL_NEGATIVE_KEY_WORDS
        );
        if ($matchGlobalNegativeKeyWords) {
            throw new GlobalMatchException('match negative key words');
        }

        return true;
    }

    /**
     * @param Product $product
     * @return bool
     * @throws GlobalMatchExceptionBrand
     * @throws \Doctrine\DBAL\DBALException
     */
    public function matchGlobalNegativeBrandWords(Product $product)
    {
        if (!strlen($product->getBrand())) {
            return false;
        }
        $prepareDataForGINSearch = $this->prepareProductBrandForMatching($product->getBrand());
        if (!strlen($prepareDataForGINSearch)) {
            return false;
        }
        $matchGlobalNegativeKeyWords = $this->getCategoryRepository()->matchGlobalNegativeKeyWords(
            $prepareDataForGINSearch,
            AdminConfiguration::GLOBAL_NEGATIVE_BRAND_KEY_WORDS
        );
        if ($matchGlobalNegativeKeyWords) {
            throw new GlobalMatchExceptionBrand('match negative key words brand');
        }

        return true;
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

    /**
     * @param string|null $brand
     * @return string|string[]|null
     */
    private function prepareProductBrandForMatching(?string $brand)
    {
        if (!$brand) {
            return null;
        }
        return preg_replace(
            '/-+/',
            '-',
            preg_replace('/ |\(|\)|\.|\!|\:|"|\'|&/','-', $brand)
        );
    }

    /**
     * @param string $sentence
     * @return string
     */
    private function prepareProductDataForMatching(string $sentence, bool $strict = true, int $limitations = 4): string
    {
        $productData = $this->helper->pregWordsFromDictionary(
            $sentence
        );
        $matchData = preg_replace('!\s+!', ',', $productData['result']);
        $matchData = strip_tags($matchData);

        $prepareDataForGINSearch = $this->prepareDataForGINSearch($matchData, $limitations);
        $prepareDataForGINSearch = $this->helper
            ->handleSearchValue($prepareDataForGINSearch, $strict);
        if (isset($productData['match']) && count($productData['match'])) {
            $resultSpaceWord = array_shift($productData['match']);
            if (is_array($resultSpaceWord) && count($resultSpaceWord)) {
                $this->redisHelper->incr(date('Y-m-d') . '_pregWordsFromDictionary');
                $arrayMapSpaceWord = array_map(function ($v) {
                    return str_replace(' ', '', $v);
                }, $resultSpaceWord);
                if (strlen($prepareDataForGINSearch)) {
                    $prepareDataForGINSearch .= '|' . implode('|', $arrayMapSpaceWord);
                } else {
                    $prepareDataForGINSearch .= implode('|', $arrayMapSpaceWord);
                }
            }
        }
        return $prepareDataForGINSearch;
    }
}