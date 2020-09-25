<?php

namespace App\Controller\Rest;

use App\Cache\TagAwareQueryResultCacheCategoryConf;
use App\Controller\HoverMenuController;
use App\Document\AdrecordProduct;
use App\Document\AdtractionProduct;
use App\Document\AwinProduct;
use App\Entity\Category;
use App\Entity\CategoryRelations;
use App\Exception\ValidatorException;
use App\Repository\CategoryConfigurationsRepository;
use App\Repository\CategoryRepository;
use App\Services\Helpers;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\DBALException;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\Configuration;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\DoctrineProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class HoverMenuManagmentController extends AbstractRestController
{
    /**
     * @var CategoryConfigurationsRepository
     */
    private $categoryConfRepo;

    /**
     * @var CategoryRepository
     */
    private $categoryRepo;

    /**
     * @var TagAwareQueryResultCacheCategoryConf
     */
    private $tagAwareQueryResultCacheCategoryConf;

    /**
     * HoverMenuManagmentController constructor.
     * @param CategoryConfigurationsRepository $categoryConfRepo
     * @param CategoryRepository $categoryRepo
     * @param TagAwareQueryResultCacheCategoryConf $tagAwareQueryResultCacheCategoryConf
     */
    public function __construct(
        CategoryConfigurationsRepository $categoryConfRepo,
        CategoryRepository $categoryRepo,
        Helpers $helpers,
        TagAwareQueryResultCacheCategoryConf $tagAwareQueryResultCacheCategoryConf
    )
    {
        parent::__construct($helpers);

        $this->categoryConfRepo = $categoryConfRepo;
        $this->categoryRepo = $categoryRepo;
        $this->tagAwareQueryResultCacheCategoryConf = $tagAwareQueryResultCacheCategoryConf;
    }

    /**
     * get sub Categories.
     *
     * @Rest\Post("/api/hover_menu/sub_categories", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="HoverMenu")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object HoverMenu",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function getSubCategoriesAction(Request $request)
    {
        $subCategoriesByIds = $this->categoryRepo
            ->getSubCategoriesByIds([$request->get('category_id')]);

        return $this->createSuccessResponse($subCategoriesByIds);
    }

    /**
     * edit Category from Hover Menu.
     *
     * @Rest\Post("/api/hover_menu/edit", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="HoverMenu")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object HoverMenu",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws DBALException
     * @throws InvalidArgumentException
     * @throws \Doctrine\DBAL\Cache\CacheException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editHoverMenuAction(Request $request)
    {
        $categoryConfigurations = $this->categoryConfRepo
            ->findOneBy(['categoryId' => $request->get('category_id')]);

        $category = $categoryConfigurations->getCategoryId();
        $pkw = $this->getHelpers()
            ->handleKeyWords(
                $request->get('pkw'),
                $category->getCategoryName(),
                'positive'
            );

        $nkw = $this->getHelpers()
            ->handleKeyWords(
                $request->get('nkw'),
                $category->getCategoryName(),
                'negative'
            );

        $categoryConfigurations
            ->setKeyWords($pkw)
            ->setNegativeKeyWords($nkw);
        if ($request->get('category_position')) {
            $category->setPosition($request->get('category_position'));
        }
        if ($request->get('category_name')) {
            $category->setCategoryName($request->get('category_name'));
        }
        if ($request->get('hotCatgory')) {
            $category->setHotCategory(true);
        } else {
            $category->setHotCategory(false);
        }

        $this->categoryConfRepo->save($categoryConfigurations);

        if ($request->get('disableForParsing')) {
            $category->setDisableForParsing(true);
            $this->setUpdateDisableForParsingByIds($category, true);
        } else {
            $category->setDisableForParsing(false);
            $this->setUpdateDisableForParsingByIds($category, false);
        }

        /** @var Registry $resultCacheImpl */
        $resultCacheImpl = $this->get('doctrine');
        $objectManager = $resultCacheImpl->getManager();
        /** @var Configuration $configuration */
        $configuration = $objectManager->getConfiguration();
        /** @var DoctrineProvider $resultCacheImpl1 */
        $resultCacheImpl1 = $configuration->getResultCacheImpl();
        $resultCacheImpl1->delete(CategoryRepository::CACHE_HOT_CATEGORY_ID);
        $resultCacheImpl1->delete(CategoryRepository::CACHE_CUSTOM_CATEGORY_ID);

        $this->getTagAwareQueryResultCacheCategoryConf()
            ->getTagAwareAdapter()
            ->invalidateTags([
                CategoryConfigurationsRepository::CATEGORY_CONF_SEARCH
            ]);
        $view = $this->createSuccessResponse(
            ['test' => 1]
        );

        return $view;
    }

    /**
     * get Products.
     *
     * @Rest\Get("/api/hover_menu/th_list", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="HoverMenu")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object HoverMenu",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws DBALException
     * @throws ValidatorException
     */
    public function listThHoverMenuAction(Request $request)
    {
        $keys = HoverMenuController::getThHoverMenu();
        $view = $this->createSuccessResponse(
            $keys
        );

        return $view;
    }

    /**
     * get Products.
     *
     * @Rest\Post("/api/hover_menu/list", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="HoverMenu")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object HoverMenu",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws DBALException
     * @throws ValidatorException
     */
    public function listHoverMenuAction(Request $request)
    {
        $data = $this->categoryConfRepo
            ->getHoverMenuCategoryConf($request->request->all());

        $view = $this->createSuccessResponse(
            array_merge(
                [
                    "draw" => $request->request->get('draw'),
                    "recordsTotal" => $this->categoryConfRepo
                        ->getHoverMenuCategoryConf($request->request->all(), true, true),
                    "recordsFiltered" => $this->categoryConfRepo
                        ->getHoverMenuCategoryConf($request->request->all(), true),
                ],
                ['data' => $data]
            )
        );

        return $view;
    }

    /**
     * @return TagAwareQueryResultCacheCategoryConf
     */
    public function getTagAwareQueryResultCacheCategoryConf(): TagAwareQueryResultCacheCategoryConf
    {
        return $this->tagAwareQueryResultCacheCategoryConf;
    }

    /**
     * @param Category $category
     * @param bool $value
     * @throws DBALException
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    private function setUpdateDisableForParsingByIds(Category $category, bool $value): void
    {
        $updateIds = [$category->getId()];
        $catIds = [$category->getId()];
        while (count($catIds)) {
            $buferIds = [];

            $ids = $this->categoryRepo
                ->getSubCategoriesByIds($catIds);
            $buferIds = array_merge($buferIds, $ids);
            $updateIds = array_merge($updateIds, $ids);

            if (count($buferIds)) {
                $catIds = $buferIds;
            } else {
                $catIds = [];
            }
        }

        $this->categoryRepo
            ->updateDisableForParsingByIds($updateIds, $value);
    }
}