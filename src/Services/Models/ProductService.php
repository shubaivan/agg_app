<?php

namespace App\Services\Models;

use App\Entity\Category;
use App\Entity\Collection\AvailableToCollection;
use App\Entity\Collection\AvailableToModel;
use App\Entity\Collection\ProductBySlugCollection;
use App\Entity\Collection\ProductCollection;
use App\Entity\Collection\ProductsCollection;
use App\Entity\Collection\ProductsRawArrayCollection;
use App\Entity\Collection\SearchProducts\AdjacentProduct;
use App\Entity\Collection\SearchProducts\GroupAdjacent;
use App\Entity\Collection\SearchProducts\GroupProductEntity;
use App\Entity\Collection\Search\SearchProductCollection;
use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\UserIp;
use App\Entity\UserIpProduct;
use App\Exception\ValidatorException;
use App\QueueModel\ResourceDataRow;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\UserIpProductRepository;
use App\Services\ObjectsHandler;
use App\Util\RedisHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Cache\CacheException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ObjectRepository;
use FOS\RestBundle\Request\ParamFetcher;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductService extends AbstractModel
{
    const GROUP_IDENTITY = 'group_identity';
    const EXCLUDE_GROUP_IDENTITY = 'exclude_group_identity';
    const WITHOUT_FACET = 'without_facet';
    const SELF_PRODUCT = 'self_product';
    const FAVORITE_PRODUCT = 'favorite_product';
    const TOP_PRODUCTS = 'top_products';
    const PRODUCT_PRICE = 'product_price';
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ObjectsHandler
     */
    private $objectHandler;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ManagerShopsService
     */
    private $managerShopsService;

    /**
     * @var RedisHelper
     */
    private $redisHelper;

    /**
     * ProductService constructor.
     * @param LoggerInterface $logger
     * @param ObjectsHandler $objectHandler
     * @param EntityManagerInterface $em
     * @param RequestStack $requestStack
     * @param ManagerShopsService $managerShopsService
     * @param RedisHelper $redisHelper
     */
    public function __construct(
        LoggerInterface $logger,
        ObjectsHandler $objectHandler,
        EntityManagerInterface $em,
        RequestStack $requestStack,
        ManagerShopsService $managerShopsService,
        RedisHelper $redisHelper
    )
    {
        $this->logger = $logger;
        $this->objectHandler = $objectHandler;
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->managerShopsService = $managerShopsService;
        $this->redisHelper = $redisHelper;
    }

    /**
     * @throws DBALException
     */
    public function autoVACUUM()
    {
        $this->getProductRepository()->autoVACUUM();
    }

    /**
     * @param $uniqIdentificationQuery
     * @return array
     * @throws CacheException
     * @throws \Exception
     */
    public function facetFilters(
        $uniqIdentificationQuery
    )
    {
        $facetQueries = $this->getProductRepository()
            ->getTagAwareQueryResultCacheProduct()
            ->fetch($uniqIdentificationQuery);

        if (!is_array($facetQueries)) {
            throw new \Exception('redis key not present');
        }

        if (count($facetQueries) < 1) {
            throw new \Exception('redis key is empty');
        }

        if (!isset($facetQueries[ProductRepository::FACET_EXTRA_FIELDS_QUERY_KEY])) {
            throw new \Exception('facet key ' . ProductRepository::FACET_EXTRA_FIELDS_QUERY_KEY . ' not present');
        }

        $brandQuery = $facetQueries[ProductRepository::FACET_EXTRA_FIELDS_QUERY_KEY];
        $pregSplitFacetQuery = preg_split('/&&/', $brandQuery[0]);
        $query = preg_replace('/query=/', '', $pregSplitFacetQuery[0]);
        $params = unserialize(preg_replace('/params=/', '', $pregSplitFacetQuery[1]));
        $types = unserialize(preg_replace('/types=/', '', $pregSplitFacetQuery[2]));

        return $this->getProductRepository()
            ->facetFiltersExtraFields($query, $params, $types);
    }

    /**
     * @param ResourceDataRow $adtractionDataRow
     * @return Product
     * @throws ValidatorException
     * @throws \Exception
     */
    public function createProductFromCsvRow(ResourceDataRow $adtractionDataRow)
    {
        $this->prepareDataForExistProduct($adtractionDataRow);
        $row = $adtractionDataRow->getRow();
        /** @var Product $handleObject */
        $handleObject = $this->getObjectHandler()
            ->handleObject(
                $row,
                Product::class,
                [Product::SERIALIZED_GROUP_CREATE],
                'json'
            );
        $this->setGroupIdentity($handleObject);

        $this->getObjectHandler()
            ->validateEntity($handleObject, [Product::SERIALIZED_GROUP_CREATE_IDENTITY]);

        return $handleObject;
    }

    /**
     * @param Product $product
     * @param ParamFetcher $paramFetcher
     * @return SearchProductCollection
     * @throws CacheException
     * @throws ValidatorException
     */
    public function getRelatedProducts(Product $product, ParamFetcher $paramFetcher)
    {
        $parameterBag = new ParameterBag($paramFetcher->all());
        $searchDataForRelated = $product->getSearchDataForRelatedProductItems();
        $search = '';
        $limitation = 5;
        while (!strlen($search)) {
            $search = $this->prepareDataForGINSearch($searchDataForRelated, $limitation, true);
            --$limitation;
        }
        $parameterBag->set('strict', true);
        $parameterBag->set(self::PRODUCT_PRICE, $product->getPrice());
        $parameterBag->set(self::EXCLUDE_GROUP_IDENTITY, $product->getGroupIdentity());
        $parameterBag->set('search', $search ?? '');

        if ($product->getCategoryRelation()->count()) {
            $collection = $product->getCategoryRelation()->map(function (Category $category) {
                return $category->getId();
            });
            $parameterBag
                ->set(ProductRepository::CATEGORY_IDS, $collection->toArray());
        }

        return $this->getRelatedProductCollection($parameterBag);
    }

    /**
     * @param Product $product
     * @return ProductBySlugCollection
     * @throws DBALException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidatorException
     */
    public function getProductBySlug(Product $product)
    {
        $this->recordIpToProduct($product);

        return $this->getProductCollection($product);
    }

    /**
     * @param string $sku
     * @return Product|object|null
     */
    public function getEntityProductBySku(string $sku)
    {
        return $this->getProductRepository()
            ->findOneBy(['sku' => $sku]);
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return SearchProductCollection
     * @throws DBALException
     * @throws ValidatorException
     */
    public function searchProductsByFilter(ParamFetcher $paramFetcher)
    {
        $paramFetcher = $this->setParamFetcherData(
            $this->requestStack,
            $paramFetcher,
            ProductService::FAVORITE_PRODUCT,
            true
        );
        $collection = $this->getProductRepository()
            ->fullTextSearchByParameterFetcher($paramFetcher);
        $count = $this->getProductRepository()
            ->fullTextSearchByParameterFetcher($paramFetcher, true);

        return $this->getSearchProductCollection($count, $collection);
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return SearchProductCollection
     * @throws CacheException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidatorException
     */
    public function getProductByIp(ParamFetcher $paramFetcher)
    {
        $groupsIdentity = $this->getUserIpProductRepository()
            ->getTopProductByIp($paramFetcher, $this->getUserIp());
        $parameterBag = new ParameterBag($paramFetcher->all());
        $parameterBag->set('sort_by', 'numberOfEntries');
        $parameterBag->set(self::WITHOUT_FACET, true);
        $parameterBag->set(ProductRepository::GROUPS_IDENTITY, $groupsIdentity);
        $collection = $this->getProductRepository()
            ->fullTextSearchByParameterBag($parameterBag);

        $count = $this->getUserIpProductRepository()
            ->getCountTopProductByIp($this->getUserIp());

        return $this->getSearchProductCollection($count, $collection);
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return SearchProductCollection
     * @throws CacheException
     * @throws ValidatorException
     */
    public function getMostPopularProducts(ParamFetcher $paramFetcher)
    {
        $parameterBag = new ParameterBag($paramFetcher->all());
        $parameterBag->set(self::TOP_PRODUCTS, true);
        $parameterBag->set(ProductRepository::SORT_BY, ProductRepository::NUMBER_OF_ENTRIES);
        $parameterBag->set(ProductRepository::SORT_ORDER, Criteria::DESC);
        $parameterBag->set(self::WITHOUT_FACET, true);

        $collection = $this->getProductRepository()
            ->fullTextSearchByParameterBag($parameterBag, false, 180);
        $count = $this->getProductRepository()
            ->fullTextSearchByParameterBag($parameterBag, true, 180);

        return $this->getSearchProductCollection($count, $collection);
    }

    /**
     * @param Product $product
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function removeCustomCategoriesFromProduct(Product $product)
    {
        $collection = $product->getHoverMenuCategories();
        foreach ($collection as $category) {
            $product->removeCategoryRelation($category);
        }
        $this->getEm()->flush();
    }

    /**
     * @return UserIp|object|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function getUserIp()
    {
        $clientIp = $this->getClientIp();
        $userIp = $this->getEm()->getRepository(UserIp::class)
            ->findOneBy(['ip' => $clientIp]);
        if (!$userIp) {
            $userIp = (new UserIp())->setIp($clientIp);
            $this->getEm()->persist($userIp);
            $this->getEm()->flush();
        }

        return $userIp;
    }

    /**
     * @param Product $product
     * @return UserIpProduct
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function recordIpToProduct(Product $product)
    {
        $userIp = $this->getUserIp();

        $userIpProduct = new UserIpProduct();
        $userIpProduct
            ->setGroupIdentity($product->getGroupIdentity())
            ->setProducts($product)
            ->setIps($userIp);

        $this->getEm()->persist($userIpProduct);
        $this->getEm()->flush();

        return $userIpProduct;
    }

    /**
     * @param ResourceDataRow $adtractionDataRow
     * @return ResourceDataRow
     * @throws \Exception
     */
    private function prepareDataForExistProduct(ResourceDataRow $adtractionDataRow)
    {
        $adtractionDataRow->unsetId();
        $product = $this->matchExistProduct($adtractionDataRow->generateIdentityUniqData());
        
        if ($product && $product->getId()) {
            $adtractionDataRow->setExistProductId($product->getId());
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . date('Ymd'),
                    Shop::PREFIX_PROCESSING_MATCH_BY_IDENTITY_BY_UNIQ_DATA . $adtractionDataRow->getShop());
            $this->getRedisHelper()
                ->hIncrBy(Shop::PREFIX_HASH . $adtractionDataRow->getRedisUniqKey(),
                    Shop::PREFIX_PROCESSING_MATCH_BY_IDENTITY_BY_UNIQ_DATA . $adtractionDataRow->getFilePath());
        }

        return $adtractionDataRow;
    }
    
    /**
     * @param string $data
     * @return Product|object|null
     */
    private function matchExistProduct(string $data)
    {
        return $this->getProductRepository()->findOneBy(['identityUniqData' => $data]);
    }

    private function setGroupIdentity(Product $product)
    {
        $shop = $product->getShop();
        if ($shop) {
            call_user_func_array([$this->getManagerShopsService(), $shop], [$product]);
        }
        if (!$product->getGroupIdentity()) {
            $product->setGroupIdentity($product->getIdentityUniqData());
        }
    }

    /**
     * @param SearchProductCollection $productCollection
     * @throws ValidatorException
     */
    private function analysisSearchProductCollection(SearchProductCollection $productCollection)
    {
        foreach ($productCollection->getCollection()->getIterator() as $groupProductEntity) {
            /** @var $groupProductEntity GroupProductEntity */
            $presentAdjacentProducts = $groupProductEntity->getPresentAdjacentProducts();
            if (count($presentAdjacentProducts) > 0) {
                /** @var GroupAdjacent $handleObject */
                $handleObject = $this->getObjectHandler()
                    ->handleObject(
                        ['adjacentProducts' => $presentAdjacentProducts],
                        GroupAdjacent::class,
                        [AdjacentProduct::GROUP_GENERATE_ADJACENT]
                    );
                $groupProductEntity->setAdjacentProducts(
                    $handleObject->getAdjacentProducts()
                );
            }

            $this->analysisSearchProductOnCurrentCollection($groupProductEntity);
        }
    }

    /**
     * @param GroupProductEntity $groupProductEntity
     * @throws ValidatorException
     */
    private function analysisSearchProductOnCurrentCollection(GroupProductEntity $groupProductEntity)
    {
        $presentCurrentProduct = $groupProductEntity->getPresentCurrentProduct();
        if (count($presentCurrentProduct) > 0) {
            /** @var AdjacentProduct $handleObject */
            $handleObject = $this->getObjectHandler()
                ->handleObject(
                    $presentCurrentProduct,
                    AdjacentProduct::class,
                    [AdjacentProduct::GROUP_GENERATE_ADJACENT]
                );
            $groupProductEntity->setCurrentProduct(
                $handleObject
            );
        }
    }

    private function getFacetQueryFilter()
    {
        $encryptMainQuery = $this->getProductRepository()->getEncryptMainQuery();

        return $encryptMainQuery;
    }

    /**
     * @param ParameterBag $parameterBag
     * @return SearchProductCollection
     * @throws CacheException
     * @throws ValidatorException
     */
    private function getRelatedProductCollection(
        ParameterBag $parameterBag
    )
    {
        $collection = $this->getProductRepository()
            ->fullTextSearchByParameterBag($parameterBag);

        $count = $this->getProductRepository()
            ->fullTextSearchByParameterBag($parameterBag, true);

        return $this->getSearchProductCollection($count, $collection);
    }

    /**
     * @param Product $product
     * @param ParameterBag $parameterBag
     * @return ProductBySlugCollection
     * @throws DBALException
     * @throws ValidatorException
     */
    private function getProductCollection(Product $product)
    {
        $bagProduct = new ParameterBag([
            self::GROUP_IDENTITY => $product->getGroupIdentity(),
            self::SELF_PRODUCT => true
        ]);
        $collection = $this->getProductRepository()
            ->fullTextSearchByParameterBag($bagProduct);
        if (count($collection)) {
            $collection[0]['productById'] = $product->getId();
        }
        $currentProductCollection = $this->getSearchProductCollection(
            1, $collection
        );

        /** @var GroupProductEntity $currentProductCollectionModel */
        $currentProductCollectionModel = $currentProductCollection->getCollection()->first();

        $productByIdCollection = new ProductBySlugCollection(
            $currentProductCollection,
            $this->prepareAvailableToModel(
                $product,
                $currentProductCollectionModel->getAvailableToData()
            )
        );

        return $productByIdCollection;
    }

    /**
     * @param Product $product
     * @param array $alsoAvailableToArray
     * @return mixed
     * @throws CacheException
     * @throws ValidatorException
     */
    private function prepareAvailableToModel(Product $product, array $alsoAvailableToArray)
    {
        $shopId = $product->getShopRelation() ? $product->getShopRelation()->getId() : null;
        $availableTo = $this->getProductRepository()
            ->getAvailableTo($alsoAvailableToArray, $shopId);

        /** @var AvailableToCollection $availableToCollection */
        $availableToCollection = $this->getObjectHandler()
            ->handleObject(
                ['collection' => $availableTo],
                AvailableToCollection::class,
                [AvailableToModel::GROUP_CREATE]
            );

        return $availableToCollection;
    }

    /**
     * @param $count
     * @param $collection
     * @return SearchProductCollection
     * @throws ValidatorException
     */
    private function getSearchProductCollection($count, $collection): SearchProductCollection
    {
        $searchProductCollection = $this->getObjectHandler()
            ->handleObject(
                [
                    'count' => $count,
                    'collection' => $collection,
                    'uniqIdentificationQuery' => $this->getFacetQueryFilter()
                ],
                SearchProductCollection::class,
                [SearchProductCollection::GROUP_CREATE]
            );
        $this->analysisSearchProductCollection($searchProductCollection);

        return $searchProductCollection;
    }

    /**
     * @return Logger
     */
    protected function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @return ObjectsHandler
     */
    protected function getObjectHandler(): ObjectsHandler
    {
        return $this->objectHandler;
    }

    /**
     * @return EntityManager
     */
    protected function getEm(): EntityManager
    {
        return $this->em;
    }

    /**
     * @return ProductRepository|ObjectRepository|EntityRepository
     */
    private function getProductRepository()
    {
        return $this->getEm()->getRepository(Product::class);
    }

    /**
     * @return CategoryRepository|EntityRepository
     */
    private function getCategoryRepository()
    {
        return $this->getEm()->getRepository(Category::class);
    }

    /**
     * @return UserIpProductRepository|ObjectRepository|EntityRepository
     */
    private function getUserIpProductRepository()
    {
        return $this->getEm()->getRepository(UserIpProduct::class);
    }


    /**
     * @return RequestStack
     */
    private function getRequestStack(): RequestStack
    {
        return $this->requestStack;
    }

    /**
     * @return string|null
     */
    private function getClientIp()
    {
        return $this->getRequestStack()->getCurrentRequest()->getClientIp();
    }

    /**
     * @return ManagerShopsService
     */
    private function getManagerShopsService(): ManagerShopsService
    {
        return $this->managerShopsService;
    }

    /**
     * @return RedisHelper
     */
    private function getRedisHelper(): RedisHelper
    {
        return $this->redisHelper;
    }
}