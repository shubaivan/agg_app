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
use App\Repository\StrategiesRepository;
use App\Services\ObjectsHandler;
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
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class BrandController extends AbstractRestController
{
    /**
     * @var BrandRepository
     */
    private $brandRepository;

    /**
     * @var TagAwareQueryResultCacheBrand
     */
    private $tagAwareQueryResultCacheBrand;

    /**
     * @var TagAwareQueryResultCacheProduct
     */
    private $tagAwareQueryResultCacheProduct;

    /**
     * @var TagAwareQuerySecondLevelCacheBrand
     */
    private $tagAwareQuerySecondLevelCacheBrand;

    /**
     * @var FilesRepository
     */
    private $fileRepo;

    /**
     * @var StrategiesRepository
     */
    private $strategyRepository;

    /**
     * @var BrandStrategyRepository
     */
    private $brandStrategyRepository;

    /**
     * @var ObjectsHandler
     */
    private $objectsHandler;

    /**
     * BrandController constructor.
     * @param Helpers $helpers
     * @param BrandRepository $brandRepository
     * @param TagAwareQueryResultCacheBrand $tagAwareQueryResultCacheBrand
     * @param TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct
     * @param FilesRepository $fileRepo
     * @param TagAwareQuerySecondLevelCacheBrand $tagAwareQuerySecondLevelCacheBrand
     * @param StrategiesRepository $strategiesRepository
     * @param BrandStrategyRepository $brandStrategyRepository
     * @param ObjectsHandler $objectsHandler
     */
    public function __construct(
        Helpers $helpers,
        BrandRepository $brandRepository,
        TagAwareQueryResultCacheBrand $tagAwareQueryResultCacheBrand,
        TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct,
        FilesRepository $fileRepo,
        TagAwareQuerySecondLevelCacheBrand $tagAwareQuerySecondLevelCacheBrand,
        StrategiesRepository $strategiesRepository,
        BrandStrategyRepository $brandStrategyRepository,
        ObjectsHandler $objectsHandler
    )
    {
        parent::__construct($helpers);
        $this->brandRepository = $brandRepository;
        $this->tagAwareQueryResultCacheBrand = $tagAwareQueryResultCacheBrand;
        $this->tagAwareQueryResultCacheProduct = $tagAwareQueryResultCacheProduct;
        $this->fileRepo = $fileRepo;
        $this->tagAwareQuerySecondLevelCacheBrand = $tagAwareQuerySecondLevelCacheBrand;
        $this->strategyRepository = $strategiesRepository;
        $this->brandStrategyRepository = $brandStrategyRepository;
        $this->objectsHandler = $objectsHandler;
    }

    /**
     * get Brand bu slug.
     *
     * @Rest\Get("/admin/api/brand/{slug}", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK, serializerGroups={Brand::SERIALIZED_GROUP_BY_SLUG})
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return Brand
     */
    public function getBrandBySlugAction(Brand $brand)
    {
        return $brand;
    }

    /**
     * get Brands.
     *
     * @Rest\Post("/admin/api/brand_list", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function postBrandListAction(Request $request)
    {
        $dataTable = $this->brandRepository
            ->getDataTablesData($request->request->all());

        $view = $this->createSuccessResponse(
            array_merge(
                [
                    "draw" => $request->request->get('draw'),
                    "recordsTotal" => $this->brandRepository
                        ->getDataTablesData($request->request->all(), true, true),
                    "recordsFiltered" => $this->brandRepository
                        ->getDataTablesData($request->request->all(), true)
                ],
                ['data' => $dataTable]
            )
        );

        return $view;
    }


    /**
     * edit Brand.
     *
     * @Rest\Post("/admin/api/brand/edit", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws InvalidArgumentException
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function editBrandAction(Request $request)
    {
        /** @var Registry $registry */
        $registry = $this->get('doctrine');
        $objectManager = $registry->getManager();
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $objectManager->getConnection();
        $connection->beginTransaction(); // suspend auto-commit
        try {
            $brand = $this->brandRepository
                ->findOneBy(['id' => $request->get('brand_id')]);

            $brand
                ->setBrandName($request->get('bn'));

            if ($request->get('topBrand')) {
                $brand->setTop(true);
            } else {
                $brand->setTop(false);
            }

            $brand->setSeoTitle($request->get('brand_seo_title'));
            $brand->setSeoDescription($request->get('brand_seo_description'));

            $fileIds = $request->get('file_ids');
            if (is_array($fileIds) && count($fileIds)) {
                $files = $this->fileRepo
                    ->getByIds($fileIds);
                foreach ($files as $file) {
                    $file->setBrand($brand);
                }
            }

            if ($request->get('strategy')) {
                $strategy = $this->strategyRepository
                    ->findOneBy(['slug' => $request->get('strategy')]);
                if ($strategy) {
                    if (!$strategy->getRequiredArgs()) {
                        $requiredArgs = [];
                    } else {
                        $requiredArgs = $request->get('required_args');
                        if (!$requiredArgs) {
                            throw new \Exception('required_args is required');
                        }
                    }
                    $brandStrategy = $this->brandStrategyRepository
                        ->findOneBy(['brand' => $brand]);
                    if (!$brandStrategy) {
                        $brandStrategy = new BrandStrategy();
                        $brandStrategy
                            ->setBrand($brand);
                        $this->brandStrategyRepository
                            ->persist($brandStrategy);
                    }

                    $brandStrategy
                        ->setStrategy($strategy)
                        ->setRequiredArgs($requiredArgs);
                    $this->objectsHandler
                        ->validateEntity($brandStrategy);
                }
            }

            $objectManager->flush();
            $connection->commit();

            /** @var Registry $registry */
            $registry = $this->get('doctrine');
            $objectManager = $registry->getManager();
            /** @var Configuration $configuration */
            $configuration = $objectManager->getConfiguration();
            /** @var DoctrineProvider $resultCacheImpl */
            $resultCacheImpl = $configuration->getResultCacheImpl();
            $resultCacheImpl->delete(BrandRepository::CACHE_HOT_BRAND_IDS);
            $resultCacheImpl->delete(StrategiesRepository::SELECT_2_STRATEGIES_MODELS);

            $this->getTagAwareQueryResultCacheBrand()
                ->getTagAwareAdapter()
                ->invalidateTags([
                    BrandRepository::BRAND_FULL_TEXT_SEARCH,
                ]);

            $this->getTagAwareQueryResultCacheProduct()
                ->getTagAwareAdapter()
                ->invalidateTags([
                    ProductRepository::PRODUCT_FULL_TEXT_SEARCH,
                ]);

            $this->tagAwareQuerySecondLevelCacheBrand
                ->deleteAll();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        $view = $this->createSuccessResponse(
            ['test' => 1]
        );

        return $view;
    }

    /**
     * @return TagAwareQueryResultCacheBrand
     */
    public function getTagAwareQueryResultCacheBrand(): TagAwareQueryResultCacheBrand
    {
        return $this->tagAwareQueryResultCacheBrand;
    }

    /**
     * @return TagAwareQueryResultCacheProduct
     */
    public function getTagAwareQueryResultCacheProduct(): TagAwareQueryResultCacheProduct
    {
        return $this->tagAwareQueryResultCacheProduct;
    }
}