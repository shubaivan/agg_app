<?php

namespace App\Controller\Rest\Admin;

use App\Cache\TagAwareQueryResultCacheBrand;
use App\Cache\TagAwareQueryResultCacheProduct;
use App\Cache\TagAwareQuerySecondLevelCacheBrand;
use App\Cache\TagAwareQuerySecondLevelCacheCategory;
use App\Entity\Brand;
use App\Repository\CategoryConfigurationsRepository;
use App\Repository\CategoryRepository;
use App\Repository\FilesRepository;
use App\Repository\ProductRepository;
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
     * BrandController constructor.
     * @param Helpers $helpers
     * @param BrandRepository $brandRepository
     * @param TagAwareQueryResultCacheBrand $tagAwareQueryResultCacheBrand
     * @param TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct
     * @param FilesRepository $fileRepo
     * @param TagAwareQuerySecondLevelCacheBrand $tagAwareQuerySecondLevelCacheBrand
     */
    public function __construct(
        Helpers $helpers,
        BrandRepository $brandRepository,
        TagAwareQueryResultCacheBrand $tagAwareQueryResultCacheBrand,
        TagAwareQueryResultCacheProduct $tagAwareQueryResultCacheProduct,
        FilesRepository $fileRepo,
        TagAwareQuerySecondLevelCacheBrand $tagAwareQuerySecondLevelCacheBrand
    )
    {
        parent::__construct($helpers);
        $this->brandRepository = $brandRepository;
        $this->tagAwareQueryResultCacheBrand = $tagAwareQueryResultCacheBrand;
        $this->tagAwareQueryResultCacheProduct = $tagAwareQueryResultCacheProduct;
        $this->fileRepo = $fileRepo;
        $this->tagAwareQuerySecondLevelCacheBrand = $tagAwareQuerySecondLevelCacheBrand;
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
                    "recordsFiltered"=> $this->brandRepository
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
        try{
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

            if ($request->get('required_args')
                && $request->get('strategy')
            ) {

            }

            $objectManager->flush();
            $connection->commit();

            /** @var Registry $resultCacheImpl */
            $resultCacheImpl = $this->get('doctrine');
            $objectManager = $resultCacheImpl->getManager();
            /** @var Configuration $configuration */
            $configuration = $objectManager->getConfiguration();
            /** @var DoctrineProvider $resultCacheImpl1 */
            $resultCacheImpl1 = $configuration->getResultCacheImpl();
            $resultCacheImpl1->delete(BrandRepository::CACHE_HOT_BRAND_IDS);

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