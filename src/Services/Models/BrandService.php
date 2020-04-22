<?php

namespace App\Services\Models;

use App\Entity\Brand;
use App\Entity\Collection\BrandsCollection;
use App\Entity\Collection\CategoriesCollection;
use App\Entity\Product;
use App\Repository\BrandRepository;
use Doctrine\DBAL\Cache\CacheException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\ParameterBag;
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
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return BrandsCollection
     * @throws CacheException
     */
    public function getBrandsByFilter(ParamFetcher $paramFetcher, $count = false)
    {
        $parameterBag = new ParameterBag($paramFetcher->all());
        $parameterBag->set('strict', true);
        $countStrict = $this->getBrandRepository()
            ->fullTextSearchByParameterBag($parameterBag, true);
        if ($countStrict > 0) {
            $strictCollection = $this->getBrandRepository()
                ->fullTextSearchByParameterBag($parameterBag);

            return (new BrandsCollection($strictCollection, $countStrict));
        }
        $parameterBag->remove('strict');
        $count = $this->getBrandRepository()
            ->fullTextSearchByParameterBag($parameterBag, true);
        $collection = $this->getBrandRepository()
            ->fullTextSearchByParameterBag($parameterBag);

        return (new BrandsCollection($collection, $count));
    }

    /**
     * @param string $name
     * @return Brand|object|null
     */
    private function matchExistBrand(string $name)
    {
        return $this->getBrandRepository()
            ->findOneBy(['name' => $name]);
    }

    /**
     * @return EntityManager
     */
    protected function getEm(): EntityManager
    {
        return $this->em;
    }

    /**
     * @return BrandRepository|ObjectRepository|EntityRepository
     */
    private function getBrandRepository()
    {
        return $this->getEm()->getRepository(Brand::class);
    }
}
