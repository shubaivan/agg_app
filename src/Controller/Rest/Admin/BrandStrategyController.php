<?php

namespace App\Controller\Rest\Admin;

use App\Cache\TagAwareQueryResultCacheBrand;
use App\Cache\TagAwareQueryResultCacheProduct;
use App\Cache\TagAwareQuerySecondLevelCacheBrand;
use App\Cache\TagAwareQuerySecondLevelCacheCategory;
use App\Entity\Brand;
use App\Entity\BrandStrategy;
use App\Entity\Strategies;
use App\Repository\BrandStrategyRepository;
use App\Repository\CategoryConfigurationsRepository;
use App\Repository\CategoryRepository;
use App\Repository\FilesRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use App\Repository\StrategiesRepository;
use App\Services\Models\Shops\Strategies\Common\AbstractStrategy;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Controller\Rest\AbstractRestController;
use App\Repository\BrandRepository;
use App\Services\Helpers;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\DoctrineProvider;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class BrandStrategyController extends AbstractRestController
{
    /**
     * @var BrandStrategyRepository
     */
    private $brandStrategyRepository;

    /**
     * @var BrandRepository
     */
    private $brandRepository;

    /**
     * @var StrategiesRepository
     */
    private $strategiesRepository;

    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * BrandStrategyController constructor.
     * @param BrandStrategyRepository $brandStrategyRepository
     * @param BrandRepository $brandRepository
     * @param StrategiesRepository $strategiesRepository
     * @param ShopRepository $shopRepository
     */
    public function __construct(
        Helpers $helpers,
        BrandStrategyRepository $brandStrategyRepository,
        BrandRepository $brandRepository,
        StrategiesRepository $strategiesRepository,
        ShopRepository $shopRepository
    )
    {
        parent::__construct($helpers);
        $this->brandStrategyRepository = $brandStrategyRepository;
        $this->brandRepository = $brandRepository;
        $this->strategiesRepository = $strategiesRepository;
        $this->shopRepository = $shopRepository;
    }


    /**
     * get Brand Strategy by slug.
     *
     * @Rest\Post("/admin/api/brand_strategy", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK, serializerGroups={
     *     Strategies::SERIALIZED_GROUP_GET_BY_SLUG,
     *     BrandStrategy::SERIALIZED_GROUP_BY_RELATION
     *     })
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return BrandStrategy|array
     * @throws \Exception
     */
    public function getBrandStrategyAction(Request $request)
    {
        $brandSlug = $request->get('brand_slug');
        $strategySlug = $request->get('strategy_slug');
        $resourceShopSlug = $request->get('resource_shop_slug');

        if (!$brandSlug || !$strategySlug || !$resourceShopSlug) {
            throw new \Exception('required data was not found');
        }
        $brand = $this->brandRepository
            ->findOneBy(['slug' => $brandSlug]);
        $strategy = $this->strategiesRepository
            ->findOneBy(['slug' => $strategySlug]);
        $shop = $this->shopRepository
            ->findOneBy(['slug' => $resourceShopSlug]);

        if (!$brand || !$strategy || !$shop) {
            throw new \Exception('model by slug was not found');
        }

        $brandStrategy = $this->brandStrategyRepository
            ->findOneBy(['brand' => $brand, 'strategy' => $strategy, 'shop' => $shop]);

        return $brandStrategy ?? [];
    }
}