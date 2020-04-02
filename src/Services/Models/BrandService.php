<?php

namespace App\Services\Models;

use App\Entity\Brand;
use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BrandService
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
     * @return Brand
     * @throws \Doctrine\ORM\ORMException
     */
    public function createBrandFromProduct(Product $product)
    {
        if (strlen($product->getBrand()) < 1) {
            throw new BadRequestHttpException('product id:' . $product->getId() . ' brand is empty');
        }
        $brand = $this->matchExistBrand($product->getBrand());
        if (!($brand instanceof Brand)) {
            $brand = new Brand();
            $brand
                ->setName($product->getBrand());
        }
        $product->setBrandRelation($brand);

        return $brand;
    }

    /**
     * @param string $name
     * @return Brand|object|null
     */
    private function matchExistBrand(string $name)
    {
        return $this->getEm()->getRepository(Brand::class)
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
