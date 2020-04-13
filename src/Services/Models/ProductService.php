<?php

namespace App\Services\Models;

use App\Entity\Collection\ProductCollection;
use App\Entity\Collection\ProductsCollection;
use App\Entity\Collection\SearchProductCollection;
use App\Entity\Product;
use App\Entity\UserIp;
use App\Entity\UserIpProduct;
use App\Exception\ValidatorException;
use App\QueueModel\AdtractionDataRow;
use App\Repository\ProductRepository;
use App\Repository\UserIpProductRepository;
use App\Repository\UserIpRepository;
use App\Services\ObjectsHandler;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
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

    /**
     * @param Product $product
     * @return ProductCollection
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getProductCollection(Product $product)
    {
        $parameterBag = new ParameterBag([
            'page' => 1,
            'count' => 4,
            'exclude_ids' => [$product->getId()],
            'search' => $product->getSearchDataForRelatedProductItems()
        ]);
        $this->recordIpToProduct($product);
        return (new ProductCollection(
            $this->getProductRepository()
                ->fullTextSearchByParameterBagOptimization($parameterBag),
            $product
        ));
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return SearchProductCollection
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getProductByIp(ParamFetcher $paramFetcher)
    {
        $collection = $this->getUserIpProductRepository()->getTopProductByIp($this->getUserIp(), $paramFetcher);
        $count = $this->getUserIpProductRepository()->getCountTopProductByIp($this->getUserIp());

        return (new SearchProductCollection($collection, $count));
    }

    /**
     * @return UserIp|object|null
     */
    private function getUserIp()
    {
        $clientIp = $this->getClientIp();
        return $this->getEm()->getRepository(UserIp::class)
            ->findOneBy(['ip' => $clientIp]);
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
        if (!$userIp) {
            $clientIp = $this->getClientIp();
            $userIp = (new UserIp())->setIp($clientIp);
            $this->getEm()->persist($userIp);
        }
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