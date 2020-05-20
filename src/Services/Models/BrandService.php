<?php

namespace App\Services\Models;

use App\Cache\TagAwareQueryResultCacheProduct;
use App\Entity\Brand;
use App\Entity\Collection\BrandsCollection;
use App\Entity\Collection\Search\SearchBrandsCollection;
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
                ->setBrandName($product->getBrand());
        }
        $product->setBrandRelation($brand);

        return $brand;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return SearchBrandsCollection
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

            return (new SearchBrandsCollection($strictCollection, $countStrict));
        }
        $parameterBag->remove('strict');
        $count = $this->getBrandRepository()
            ->fullTextSearchByParameterBag($parameterBag, true);
        $collection = $this->getBrandRepository()
            ->fullTextSearchByParameterBag($parameterBag);

        return (new SearchBrandsCollection($collection, $count));
    }

    /**
     * @param $uniqIdentificationQuery
     * @param ParamFetcher $paramFetcher
     * @return SearchBrandsCollection
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
            throw new \Exception('redis key not present');
        }

        if (count($facetQueries) < 1) {
            throw new \Exception('redis key is empty');
        }

        if (!isset($facetQueries[ProductRepository::FACET_BRAND_QUERY_KEY])) {
            throw new \Exception('facet key ' . ProductRepository::FACET_BRAND_QUERY_KEY . ' not present');
        }

        $brandQuery = $facetQueries[ProductRepository::FACET_BRAND_QUERY_KEY];
        $pregSplitBrandQuery = preg_split('/&/', $brandQuery[0]);
        $query = preg_replace('/query=/', '', $pregSplitBrandQuery[0]);
        $params = unserialize(preg_replace('/params=/', '', $pregSplitBrandQuery[1]));
        $types = unserialize(preg_replace('/types=/', '', $pregSplitBrandQuery[2]));

        $facetFiltersBrand = $this->getBrandRepository()
            ->facetFiltersBrand(
                (new ParameterBag($paramFetcher->all())),
                $query,
                $params,
                $types
            );

        $facetFiltersBrandCountQuery = preg_replace(
            '/SELECT(.|\n*)+FROM/',
            'SELECT COUNT(DISTINCT brand_alias.id) FROM ',
            $query
        );

        $facetFiltersBrandCount = $this->getBrandRepository()
            ->facetFiltersBrand(
                (new ParameterBag($paramFetcher->all())),
                $facetFiltersBrandCountQuery,
                $params,
                $types,
                true
            );

        $brandsCollection = new SearchBrandsCollection($facetFiltersBrand, $facetFiltersBrandCount);

        return $brandsCollection;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return Brand[]|BrandsCollection|int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBrandsByIds(ParamFetcher $paramFetcher)
    {
        $collection = $this->getBrandRepository()
            ->getBrandByIds($paramFetcher);
        $count = $this->getBrandRepository()
            ->getBrandByIds($paramFetcher, true);
        $collection = new BrandsCollection($collection, $count);

        return $collection;
    }

    /**
     * @param string $name
     * @return Brand|object|null
     */
    private function matchExistBrand(string $name)
    {
        return $this->getBrandRepository()
            ->findOneBy(['brandName' => $name]);
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
