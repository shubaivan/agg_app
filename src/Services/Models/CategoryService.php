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
use App\Exception\ValidatorException;
use App\Repository\CategoryConfigurationsRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Services\Helpers;
use App\Services\ObjectsHandler;
use App\Util\RedisHelper;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\DBAL\Cache\CacheException;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\ParameterBag;

class CategoryService extends AbstractModel
{
    const MAIN_SEARCH = 'main_search';
    const MAIN_CATEGORY_SEARCH = 'main_category_search';
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
     * @param SlugifyInterface $cs
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ObjectsHandler $objecHandler,
        TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct,
        Helpers $helper,
        RedisHelper $redisHelper,
        CategoryConfigurationsRepository $categoryConfigurationsRepository,
        SlugifyInterface $cs
    )
    {
        parent::__construct($cs);
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

        return  $this->matchCategoryWithSub($parameterBag, true);
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
        $parameterBag = new ParameterBag();
        $parameterBag->set(self::MAIN_SEARCH, [$category->getId()]);
        $analysisProductByMainCategory = $this->analysisProductByMainCategory(
            $parameterBag,
            $product,
            true
        );

        return $analysisProductByMainCategory;
    }

    /**
     * @param ParameterBag $parameterBag
     * @param bool $explain
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\DBALException
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
     * @return array|mixed[]|void
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handleAnalysisProductByMainCategory(Product $product)
    {
        $parameterBag = new ParameterBag();
        if (!strlen($product->getCategory()) && !strlen($product->getShop())) {
            return [];
        }
//        $prepareCategoryDataForGINSearch = $this->prepareProductDataForMatching(
//            $product->getCategory() .','.$product->getShop(), false, 2
//        );
//        if (!strlen($prepareCategoryDataForGINSearch)) {
//            return [];
//        }
        
        $isMatchToMainCategory = $this->getCategoryRepository()
            ->isMatchToMainCategory($product->getCategoryWithShop());

        if (!$isMatchToMainCategory) {
            return [];
        }
//        $mainCategoryWords = $this->getCategoryRepository()
//            ->getMainSubCategoryIds();
//
//        if (!count($mainCategoryWords)) {
//            return [];
//        }

//        $mainCategoryIds = $this->getCategoryRepository()
//            ->isMatchPlainCategoriesString(
//                $product->getCategory() .','.$product->getShop(),
//                $mainCategoryWords,
//                true
//        );


        
        $mainCategoryIds = [];
        array_map(function ($v) use (&$mainCategoryIds){
            if (isset($v['id'])) {
                $mainCategoryIds[] = $v['id'];
            }
        }, $isMatchToMainCategory);

        if (!$mainCategoryIds) {
            return [];
        }
        $parameterBag->set(self::MAIN_SEARCH, $mainCategoryIds);
        $resultAnalysis = $this->analysisProductByMainCategory(
            $parameterBag, $product
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
     * @param ParameterBag $parameterBag
     * @param Product $product
     * @param bool $explain
     * @return array|mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function analysisProductByMainCategory(
        ParameterBag $parameterBag,
        Product $product,
        bool $explain = false
    )
    {
        $result = [];
        $mainCategoryIds = $parameterBag->get(self::MAIN_SEARCH);
        foreach ($mainCategoryIds as $id) {
            $result[] = ['id' => $id];
        }
        $extras = $product->getExtras();
        if (isset($extras[Product::SIZE])) {
            $sizeCategoriesIds = $this->categoryConfigurationsRepository
                ->matchSizeCategories($extras[Product::SIZE], $mainCategoryIds);
            if (count($sizeCategoriesIds)) {
                $result = array_merge($sizeCategoriesIds, $result);
            }
        }

        $prepareDataForGINSearch = $this->prepareProductDataForMatching(
            $product->getName() . ', ' . $product->getDescription(),
            true,
            3
        );
        if ($prepareDataForGINSearch) {
            $resultData = $prepareDataForGINSearch;
            $parameterBag->set(self::SUB_MAIN_SEARCH, $resultData);
            $matchCategoryWithoutSub = $this->matchCategoryWithSub($parameterBag, $explain);

            if (!$matchCategoryWithoutSub) {
                return  $result;
            }
            $result = array_merge($result, $matchCategoryWithoutSub);
            $parameterBag->set(self::SUB_SUB_MAIN_SEARCH, $resultData);
            $matchCategoryWithSub = $this->matchCategoryWithSub($parameterBag, $explain);

            if (!$matchCategoryWithSub) {
                return  $result;
            }
            $result = array_merge($result, $matchCategoryWithSub);
        }

        return $result;
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
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function createArrayModelCategory(array $arrayCategories, Product $product)
    {
        $arrayModelsCategory = [];
        foreach ($arrayCategories as $category) {

            try {
                $categoryModel = $this->matchExistCategory($category);
                if (!($categoryModel instanceof Category)) {
                    $categoryModel = new Category();
                    $categoryModel
                        ->setCategoryName($category);

                    $this->getObjecHandler()
                        ->validateEntity($categoryModel, [Category::SERIALIZED_GROUP_CREATE]);
                }
            } catch (ValidatorException $e) {
                $categoryModel = $this->matchExistCategory($category);
            }
            
            if ($categoryModel instanceof Category) {
                if ($categoryModel->getSubCategoryRelations()->count()) {
                    foreach ($categoryModel->getSubCategoryRelations()->getIterator() as $categoryRelation) {
                        /** @var $categoryRelation CategoryRelations */
                        if ($categoryRelation->getMainCategory()) {
                            $product->addCategoryRelation($categoryRelation->getMainCategory());
                        }
                    }
                }
//            if ($categoryModel->getSubCategoryRelations()->count()) {
//                $mainCategoryChallengerIds = [];
//                foreach ($categoryModel->getSubCategoryRelations()->getIterator() as $categoryRelation) {
//                    /** @var $categoryRelation CategoryRelations */
//                    if ($categoryRelation->getMainCategory()) {
//                        $mainCategoryChallengerIds[$categoryRelation->getMainCategory()->getId()] = $categoryRelation->getMainCategory();
//                    }
//                }
//                if (count($mainCategoryChallengerIds)) {
//                    $prepareDataForGINSearch = $this->prepareProductDataForMatching(
//                        $product->getName() . ', ' . $product->getDescription()
//                    );
//                    $matchSeparateCategoryById = $this->getCategoryRepository()
//                        ->matchSeparateCategoryById(
//                            array_keys($mainCategoryChallengerIds),
//                            $prepareDataForGINSearch
//                        );
//                    if (count($matchSeparateCategoryById)) {
//                        foreach ($matchSeparateCategoryById as $id) {
//                            if (isset($id['id']) && isset($mainCategoryChallengerIds[$id['id']])) {
//                                $product->addCategoryRelation($mainCategoryChallengerIds[$id['id']]);
//                            }
//                        }
//                    }
//                }
//            }
                
                $product->addCategoryRelation($categoryModel);
                array_push($arrayModelsCategory, $categoryModel);   
            }
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
        $prepareDataForGINSearch = $this->prepareProductDataForMatching(
            $productComparingData, true, 3
        );
        if (!strlen($prepareDataForGINSearch)) {
            return false;
        }
        $matchGlobalNegativeKeyWords = $this->getCategoryRepository()->matchKeyWordsByProperty(
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
        $matchGlobalNegativeKeyWords = $this->getCategoryRepository()
            ->matchKeyWordsByProperty(
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
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function matchExistCategory(string $name)
    {
        return $this->getCategoryRepository()
            ->matchExistBySlug($this->generateSlugForString($name));
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
        $prepareDataForGINSearch = '';
        if (strlen($productData['result']) && strlen($productData['result']) >= $limitations) {
            $matchData = preg_replace('!\s+!', ',', $productData['result']);
            $matchData = strip_tags($matchData);

            $prepareDataForGINSearch = $this->prepareDataForGINSearch($matchData, $limitations);
            if ($prepareDataForGINSearch) {
                $prepareDataForGINSearch = $this->helper
                    ->handleSearchValue($prepareDataForGINSearch, $strict);
            }
        }

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