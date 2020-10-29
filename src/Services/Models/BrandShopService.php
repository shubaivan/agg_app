<?php


namespace App\Services\Models;


use App\Entity\Brand;
use App\Entity\BrandShop;
use App\Entity\Product;
use App\Entity\Shop;
use App\Repository\BrandShopRepository;
use App\Services\ObjectsHandler;

class BrandShopService
{
    /**
     * @var BrandShopRepository
     */
    private $brandShopRepository;

    /**
     * @var ObjectsHandler
     */
    private $objectsHandler;

    /**
     * BrandShopService constructor.
     * @param BrandShopRepository $brandShopRepository
     * @param ObjectsHandler $objectsHandler
     */
    public function __construct(BrandShopRepository $brandShopRepository, ObjectsHandler $objectsHandler)
    {
        $this->brandShopRepository = $brandShopRepository;
        $this->objectsHandler = $objectsHandler;
    }

    /**
     * @param Product $product
     * @throws \App\Exception\ValidatorException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     */
    public function createBrandShopRelation(Product $product)
    {
        $brand = $product->getBrandRelation();
        $shop = $product->getShopRelation();
        if ((!$brand && !$brand->getSlug()) || (!$shop && !$shop->getSlug())) {
            return;
        }
        $brandShop = $this->getBrandShopRepository()->checkExistBrandShopRelation($brand, $shop);
        if (!$brandShop) {
            $brandShop = new BrandShop();
            $brandShop
                ->setBrand($brand)
                ->setShop($shop);

            $this->getObjectsHandler()
                ->validateEntity($brandShop);

            $this->getBrandShopRepository()
                ->getPersist($brandShop);
        }
    }

    /**
     * @return ObjectsHandler
     */
    private function getObjectsHandler(): ObjectsHandler
    {
        return $this->objectsHandler;
    }

    /**
     * @return BrandShopRepository
     */
    private function getBrandShopRepository(): BrandShopRepository
    {
        return $this->brandShopRepository;
    }
}