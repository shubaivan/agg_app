<?php

namespace App\Services\Models;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Collection\ProductCollection;
use App\Entity\Collection\ProductsCollection;
use App\Entity\Collection\SearchProductCollection;
use App\Entity\Product;
use App\Entity\UserIp;
use App\Entity\UserIpProduct;
use App\Exception\ValidatorException;
use App\QueueModel\AdtractionDataRow;
use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\UserIpProductRepository;
use App\Repository\UserIpRepository;
use App\Services\ObjectsHandler;
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

class ProductService
{
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
     * ProductService constructor.
     * @param LoggerInterface $logger
     * @param ObjectsHandler $objectHandler
     * @param EntityManagerInterface $em
     * @param RequestStack $requestStack
     */
    public function __construct(
        LoggerInterface $logger,
        ObjectsHandler $objectHandler,
        EntityManagerInterface $em,
        RequestStack $requestStack
    )
    {
        $this->logger = $logger;
        $this->objectHandler = $objectHandler;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    /**
     * @param AdtractionDataRow $adtractionDataRow
     * @return Product
     * @throws ValidatorException
     * @throws \Doctrine\ORM\ORMException
     */
    public function createProductFromAndractionCsvRow(AdtractionDataRow $adtractionDataRow)
    {
        $this->prepareDataForExistProduct($adtractionDataRow);
        $row = $adtractionDataRow->getRow();
        /** @var Product $handleObject */
        $handleObject = $this->getObjectHandler()
            ->handleObject($row, Product::class, [Product::SERIALIZED_GROUP_CREATE]);

        return $handleObject;
    }

//    public function getProductsByFilter(ParamFetcher $paramFetcher, $count = false)
//    {
//        $parameterBag = new ParameterBag($paramFetcher->all());
//        if ($paramFetcher->get('category_word')) {
//
//            $this->getCategoryRepository()
//                ->fullTextSearchByParameterBag()
//        }
//        return $this->fullTextSearchByParameterBag($parameterBag, $count);
//    }

    /**
     * @param Product $product
     * @return ProductCollection
     * @throws DBALException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getProductById(Product $product)
    {
        $parameterBag = new ParameterBag([
            'page' => 1,
            'count' => 4,
            'exclude_ids' => [$product->getId()],
            'search' => $product->getSearchDataForRelatedProductItems()
        ]);
        $this->recordIpToProduct($product);

        return $this->getProductCollection($product, $parameterBag);
    }

    /**
     * @param Product $product
     * @param ParameterBag $parameterBag
     * @return ProductCollection
     * @throws DBALException
     */
    private function getProductCollection(Product $product, ParameterBag $parameterBag)
    {
        return (new ProductCollection(
            $this->getProductRepository()
                ->fullTextSearchByParameterBag($parameterBag),
            $product
        ));
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return SearchProductCollection
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getProductByIp(ParamFetcher $paramFetcher)
    {
        $collection = $this->getUserIpProductRepository()
            ->getTopProductByIp($this->getUserIp(), $paramFetcher);
        $count = $this->getUserIpProductRepository()
            ->getCountTopProductByIp($this->getUserIp());

        return (new SearchProductCollection($collection, $count));
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
            ->setProducts($product)
            ->setIps($userIp);

        $this->getEm()->persist($userIpProduct);
        $this->getEm()->flush();

        return $userIpProduct;
    }

    /**
     * @param AdtractionDataRow $adtractionDataRow
     * @return AdtractionDataRow
     */
    private function prepareDataForExistProduct(AdtractionDataRow $adtractionDataRow)
    {
        $product = $this->matchExistProduct($adtractionDataRow->getSku());
        if ($product && $product->getId()) {
            $adtractionDataRow->setExistProductId($product->getId());
        }

        return $adtractionDataRow;
    }

    /**
     * @param string $sku
     * @return Product|object|null
     */
    private function matchExistProduct(string $sku)
    {
        return $this->getProductRepository()->findOneBy(['sku' => $sku]);
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
}