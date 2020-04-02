<?php

namespace App\Services\Models;

use App\Entity\Product;
use App\Entity\Shop;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ShopService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * BrandService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Product $product
     * @return Shop|object|null
     * @throws \Doctrine\ORM\ORMException
     */
    public function createShopFromProduct(Product $product)
    {
        if (strlen($product->getShop()) < 1) {
            throw new BadRequestHttpException('product id:' . $product->getId() . ' shop is empty');
        }
        $shop = $this->matchExistShop($product->getShop());
        if (!($shop instanceof Shop)) {
            $shop = new Shop();
            $shop
                ->setName($product->getShop());
        }
        $product->setShopRelation($shop);

        return $shop;
    }

    /**
     * @param string $name
     * @return Shop|object|null
     */
    private function matchExistShop(string $name)
    {
        return $this->getEm()->getRepository(Shop::class)
            ->findOneBy(['name' => $name]);
    }

    /**
     * @return EntityManager
     */
    protected function getEm(): EntityManager
    {
        return $this->em;
    }
}
