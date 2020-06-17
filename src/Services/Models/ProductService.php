<?php

namespace App\Services\Models;

use App\Entity\Category;
use App\Entity\Collection\ProductByIdCollection;
use App\Entity\Collection\ProductCollection;
use App\Entity\Collection\ProductsCollection;
use App\Entity\Collection\ProductsRawArrayCollection;
use App\Entity\Collection\SearchProducts\AdjacentProduct;
use App\Entity\Collection\SearchProducts\GroupAdjacent;
use App\Entity\Collection\SearchProducts\GroupProductEntity;
use App\Entity\Collection\Search\SearchProductCollection;
use App\Entity\Product;
use App\Entity\UserIp;
use App\Entity\UserIpProduct;
use App\Exception\ValidatorException;
use App\QueueModel\ResourceDataRow;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\UserIpProductRepository;
use App\Services\ObjectsHandler;
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
     * ProductService constructor.
     * @param LoggerInterface $logger
     * @param ObjectsHandler $objectHandler
     * @param EntityManagerInterface $em
     * @param RequestStack $requestStack
     * @param ManagerShopsService $managerShopsService
     */
    public function __construct(
        LoggerInterface $logger,
        ObjectsHandler $objectHandler,
        EntityManagerInterface $em,
        RequestStack $requestStack,
        ManagerShopsService $managerShopsService
    )
    {
        $this->logger = $logger;
        $this->objectHandler = $objectHandler;
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->managerShopsService = $managerShopsService;
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
        $pregSplitBrandQuery = preg_split('/&&/', $brandQuery[0]);
        $query = preg_replace('/query=/', '', $pregSplitBrandQuery[0]);
        $params = unserialize(preg_replace('/params=/', '', $pregSplitBrandQuery[1]));
        $types = unserialize(preg_replace('/types=/', '', $pregSplitBrandQuery[2]));

        return $this->getProductRepository()
            ->facetFiltersExtraFields($query, $params, $types);
    }

    /**
     * @param ResourceDataRow $adtractionDataRow
     * @return Product
     * @throws ValidatorException
     * @throws \Doctrine\ORM\ORMException
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
     * @return ProductByIdCollection
     * @throws DBALException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidatorException
     */
    public function getProductById(Product $product)
    {
        $searchDataForRelated = $product->getSearchDataForRelatedProductItems();
        $search = $this->prepareDataForGINSearch($searchDataForRelated);

        $parameterBag = new ParameterBag([
            'page' => 1,
            'count' => 4,
            self::EXCLUDE_GROUP_IDENTITY => $product->getGroupIdentity(),
            'search' => $search ?? ''
        ]);
        $this->recordIpToProduct($product);

        return $this->getProductCollection($product, $parameterBag);
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
     * @return ProductsRawArrayCollection
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getMostPopularProducts(ParamFetcher $paramFetcher)
    {
        $collection = $this->getUserIpProductRepository()
            ->getTopProductByIp($paramFetcher);



        $count = $this->getUserIpProductRepository()
            ->getCountTopProductByIp();

        return (new ProductsRawArrayCollection($collection, $count));
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
     */
    private function prepareDataForExistProduct(ResourceDataRow $adtractionDataRow)
    {
        $product = $this->matchExistProduct($adtractionDataRow->getSku());

        if ($product && $product->getId()) {
            if ($product->getShop() == $adtractionDataRow->getShop()) {
                $adtractionDataRow->setExistProductId($product->getId());
            } else {
                $newSku = $this->modifyToUniqSku($adtractionDataRow->getSku());
                $adtractionDataRow->setSku($newSku);
            }
        }

        return $adtractionDataRow;
    }

    private function modifyToUniqSku(string $sku)
    {
        $modifySku = $sku . '_1';
        $matchSku = $this->getProductRepository()
            ->findOneBy(['sku' => $modifySku]);;
        while ($matchSku) {
            $modifySku = $modifySku . '1';
            $matchSku = $this->getProductRepository()
                ->findOneBy(['sku' => $modifySku]);
        }

        return $modifySku;
    }

    /**
     * @param string $sku
     * @return Product|object|null
     */
    private function matchExistProduct(string $sku)
    {
        return $this->getProductRepository()->findOneBy(['sku' => $sku]);
    }

    private function setGroupIdentity(Product $product)
    {
        $shop = $product->getShop();
        if ($shop) {
            call_user_func_array([$this->getManagerShopsService(), $shop], [$product]);
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
     * @param Product $product
     * @param ParameterBag $parameterBag
     * @return ProductByIdCollection
     * @throws DBALException
     * @throws ValidatorException
     */
    private function getProductCollection(Product $product, ParameterBag $parameterBag)
    {
        $bagProduct = new ParameterBag([self::GROUP_IDENTITY => $product->getGroupIdentity()]);
        $collection = $this->getProductRepository()
            ->fullTextSearchByParameterBag($bagProduct);
        if (count($collection)) {
            $collection[0]['productById'] = $product->getId();
        }
        $currentProductCollection = $this->getSearchProductCollection(1, $collection);

        $collection = $this->getProductRepository()
            ->fullTextSearchByParameterBag($parameterBag);

        $relatedItemProductCollection = $this->getSearchProductCollection(
            $parameterBag->get('count'), $collection);

        $productByIdCollection = new ProductByIdCollection(
            $currentProductCollection,
            $relatedItemProductCollection
        );

        return $productByIdCollection;
    }

    public function getProductByGroupsIdentity()
    {

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
}