<?php

namespace App\Services\Models;

use App\Cache\TagAwareQueryResultCacheProduct;
use App\Entity\Brand;
use App\Entity\Collection\BrandsCollection;
use App\Entity\Collection\CategoriesCollection;
use App\Entity\Product;
use App\Repository\BrandRepository;
use App\Repository\ProductRepository;
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
     * @var TagAwareQueryResultCacheProduct
     */
    private $tagAwareQueryResultCacheProduct;

    /**
     * BrandService constructor.
     * @param EntityManagerInterface $em
     * @param TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct
     */
    public function __construct(
        EntityManagerInterface $em,
        TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct
    )
    {
        $this->em = $em;
        $this->tagAwareQueryResultCacheProduct = $tagAwareQueryResultCacheProduct;
    }

    /**
     * @param Product $product
     * @return Brand|bool
     * @throws \Doctrine\ORM\ORMException
     */
    public function createBrandFromProduct(Product $product)
    {
        if (strlen($product->getBrand()) < 1) {
            return false;
//            throw new BadRequestHttpException('product id:' . $product->getId() . ' brand is empty');
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
     * @param $uniqIdentificationQuery
     * @param ParamFetcher $paramFetcher
     * @return BrandsCollection
     * @throws CacheException
     * @throws \Exception
     */
    public function facetFilters(
        $uniqIdentificationQuery,
        ParamFetcher $paramFetcher
    )
    {
        $facetQueries = $this->getTagAwareQueryResultCacheProduct()
            ->fetch($uniqIdentificationQuery);

        if (!is_array($facetQueries)) {
            throw new \Exception('facet key empty');
        }

        if (count($facetQueries) < 1) {
            throw new \Exception('facet key empty');
        }

        if (!isset($facetQueries[ProductRepository::FACET_BRAND_QUERY_KEY])) {
            throw new \Exception('facet key empty');
        }

        $brandQuery = $facetQueries[ProductRepository::FACET_BRAND_QUERY_KEY];
        $pregSplitBrandQuery = preg_split('/&/', $brandQuery[0]);
        $query = preg_replace('/query=/', '', $pregSplitBrandQuery[0]);
        $params = unserialize(preg_replace('/params=/', '', $pregSplitBrandQuery[1]));
        $types = unserialize(preg_replace('/types=/', '', $pregSplitBrandQuery[2]));

        $facetFiltersBrandCountQuery = preg_replace(
            '/SELECT(.|\n*)+FROM/',
            'SELECT COUNT(DISTINCT brand_alias.id) FROM ',
            $query
        );

        $facetFiltersBrand = $this->getBrandRepository()
            ->facetFiltersBrand(
                (new ParameterBag($paramFetcher->all())),
                $query,
                $params,
                $types
            );

        $facetFiltersBrandCount = $this->getBrandRepository()
            ->facetFiltersBrand(
                (new ParameterBag($paramFetcher->all())),
                $facetFiltersBrandCountQuery,
                $params,
                $types,
                true
            );

        $brandsCollection = new BrandsCollection($facetFiltersBrand, $facetFiltersBrandCount);

        return $brandsCollection;
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

    /**
     * @return TagAwareQueryResultCacheProduct
     */
    private function getTagAwareQueryResultCacheProduct(): TagAwareQueryResultCacheProduct
    {
        return $this->tagAwareQueryResultCacheProduct;
    }
}
