<?php

namespace App\Services\Models;

use App\Entity\Product;
use App\Exception\ValidatorException;
use App\QueueModel\AdtractionDataRow;
use App\Services\ObjectsHandler;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

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
     * ProductService constructor.
     * @param LoggerInterface $logger
     * @param ObjectsHandler $objectHandler
     * @param EntityManagerInterface $em
     */
    public function __construct(
        LoggerInterface $logger,
        ObjectsHandler $objectHandler,
        EntityManagerInterface $em
    )
    {
        $this->logger = $logger;
        $this->objectHandler = $objectHandler;
        $this->em = $em;
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
        $this->getEm()->persist($handleObject);

        return $handleObject;
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
        $productRepository = $this->getEm()->getRepository(Product::class);

        return $productRepository->findOneBy(['sku' => $sku]);
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
}