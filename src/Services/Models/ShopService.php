<?php

namespace App\Services\Models;

use App\Cache\TagAwareQueryResultCacheProduct;
use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Collection\BrandsCollection;
use App\Entity\Collection\Search\SearchProductCollection;
use App\Entity\Collection\Search\SearchShopsCollection;
use App\Entity\Collection\Search\SeparateShopModel;
use App\Entity\Collection\ShopsCollection;
use App\Entity\Product;
use App\Entity\Shop;
use App\Exception\ValidatorException;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use App\Services\ObjectsHandler;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\DBAL\Cache\CacheException;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ShopService extends AbstractModel
{
    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var ObjectsHandler
     */
    private $objecHandler;

    /**
     * @var TagAwareQueryResultCacheProduct
     */
    private $tagAwareQueryResultCacheProduct;

    /**
     * ShopService constructor.
     * @param ShopRepository $shopRepository
     * @param TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct
     * @param ObjectsHandler $objecHandler
     * @param SlugifyInterface $cs
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        ShopRepository $shopRepository,
        TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct,
        ObjectsHandler $objecHandler,
        SlugifyInterface $cs,
        CategoryRepository $categoryRepository
    )
    {
        parent::__construct($cs);
        $this->shopRepository = $shopRepository;
        $this->tagAwareQueryResultCacheProduct = $tagAwareQueryResultCacheProduct;
        $this->objecHandler = $objecHandler;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Product $product
     * @return Shop|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     */
    public function createShopFromProduct(Product $product)
    {
        if (strlen($product->getShop()) < 1) {
            throw new BadRequestHttpException('product id:' . $product->getId() . ' shop is empty');
        }

        try {

            $shop = $this->matchExistShop($product->getShop());
            if (!($shop instanceof Shop)) {
                $shop = new Shop();
                $shop
                    ->setShopName($product->getShop());

                $this->getObjecHandler()
                    ->validateEntity($shop);

                $this->shopRepository
                    ->getPersist($shop);
            }
        } catch (ValidatorException $e) {
            $shop = $this->matchExistShop($product->getShop());
        }

        if ($shop instanceof Shop) {
            $product->setShopRelation($shop);
        }

        return $shop;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return SearchShopsCollection
     * @throws CacheException
     * @throws ValidatorException
     */
    public function getShopsByFilter(ParamFetcher $paramFetcher)
    {
        $parameterBag = new ParameterBag($paramFetcher->all());
        $parameterBag->set('strict', true);
        $countStrict = $this->getShopRepository()
            ->fullTextSearchByParameterBag($parameterBag, true);
        if ($countStrict > 0) {
            $strictCollection = $this->getShopRepository()
                ->fullTextSearchByParameterBag($parameterBag);
            return $this->identityModelById($this->objecHandler
                ->handleObject(
                    [
                        'count' => $countStrict,
                        'collection' => $strictCollection,
                    ],
                    SearchShopsCollection::class
                ));
        }
        $parameterBag->remove('strict');
        $count = $this->getShopRepository()
            ->fullTextSearchByParameterBag($parameterBag, true);
        $collection = $this->getShopRepository()
            ->fullTextSearchByParameterBag($parameterBag);

        return $this->identityModelById($this->objecHandler
            ->handleObject(
                [
                    'count' => $count,
                    'collection' => $collection,
                ],
                SearchShopsCollection::class
            ));
    }

    /**
     * @param SearchShopsCollection $shopsCollection
     * @return SearchShopsCollection
     */
    private function identityModelById(SearchShopsCollection $shopsCollection)
    {
        $collection = $shopsCollection->getCollection();
        $mapDTO = $collection->map(function ($shopModel) {
            /** @var $shopModel SeparateShopModel */
            $categoryIds = $shopModel->getCategoryModels();
            /** @var Category[] $categoryModelsByIds */
            $categoryModelsByIds = $this->categoryRepository
                ->getCategoryModelsByIds((new ParameterBag(['ids' => $categoryIds])));
            $categoriesDTO = [];
            foreach ($categoryModelsByIds as $category) {
                $categoriesDTO[] = [
                    'id' => $category->getId(),
                    'slug' => $category->getSlug(),
                    'categoryName' => $category->getCategoryName(),
                ];
            }
            $shopModel->setCategoryModels($categoriesDTO);
            return $shopModel;
        });
        $shopsCollection->setCollection($mapDTO);

        return $shopsCollection;
    }

    /**
     * @param $uniqIdentificationQuery
     * @param ParamFetcher $paramFetcher
     * @return SearchShopsCollection
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

        if (!isset($facetQueries[ProductRepository::FACET_SHOP_QUERY_KEY])) {
            throw new \Exception('facet key ' . ProductRepository::FACET_SHOP_QUERY_KEY . ' not present');
        }

        $shopQuery = $facetQueries[ProductRepository::FACET_SHOP_QUERY_KEY];
        $pregSplitShopQuery = preg_split('/&&/', $shopQuery[0]);
        $query = preg_replace('/query=/', '', $pregSplitShopQuery[0]);
        $params = unserialize(preg_replace('/params=/', '', $pregSplitShopQuery[1]));
        $types = unserialize(preg_replace('/types=/', '', $pregSplitShopQuery[2]));

        $facetFilters = $this->getShopRepository()
            ->facetFilters(
                (new ParameterBag($paramFetcher->all())),
                $query,
                $params,
                $types
            );

        $facetFiltersBrandCountQuery = preg_replace(
            '/SELECT(.|\n*)+FROM/',
            'SELECT COUNT(DISTINCT shop_alias.id) FROM ',
            $query
        );

        $facetFiltersCount = $this->getShopRepository()
            ->facetFilters(
                (new ParameterBag($paramFetcher->all())),
                $facetFiltersBrandCountQuery,
                $params,
                $types,
                true
            );

        return $this->objecHandler
            ->handleObject(
                [
                    'count' => $facetFiltersCount,
                    'collection' => $facetFilters,
                ],
                SearchShopsCollection::class
            );
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return Shop[]|ShopsCollection|int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getShopsByIds(ParamFetcher $paramFetcher)
    {
        $collection = $this->getShopRepository()
            ->getShopsByIds($paramFetcher);
        $count = $this->getShopRepository()
            ->getShopsByIds($paramFetcher, true);
        $collection = new ShopsCollection($collection, $count);

        return $collection;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function matchExistShop(string $name)
    {
        return $this->getShopRepository()
            ->matchExistBySlug($this->generateSlugForString($name));
    }

    /**
     * @return ShopRepository
     */
    public function getShopRepository(): ShopRepository
    {
        return $this->shopRepository;
    }

    /**
     * @return TagAwareQueryResultCacheProduct
     */
    public function getTagAwareQueryResultCacheProduct(): TagAwareQueryResultCacheProduct
    {
        return $this->tagAwareQueryResultCacheProduct;
    }

    /**
     * @return ObjectsHandler
     */
    public function getObjecHandler(): ObjectsHandler
    {
        return $this->objecHandler;
    }
}
