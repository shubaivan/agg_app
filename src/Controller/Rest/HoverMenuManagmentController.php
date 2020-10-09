<?php

namespace App\Controller\Rest;

use App\Cache\TagAwareQueryResultCacheCategory;
use App\Cache\TagAwareQueryResultCacheCategoryConf;
use App\Cache\TagAwareQuerySecondLevelCacheCategory;
use App\Controller\HoverMenuController;
use App\Document\AdrecordProduct;
use App\Document\AdtractionProduct;
use App\Document\AwinProduct;
use App\Entity\Category;
use App\Entity\CategoryConfigurations;
use App\Entity\CategoryRelations;
use App\Exception\ValidatorException;
use App\Repository\CategoryConfigurationsRepository;
use App\Repository\CategoryRepository;
use App\Repository\CategorySectionRepository;
use App\Repository\FilesRepository;
use App\Services\Helpers;
use App\Services\Models\CategoryService;
use App\Services\ObjectsHandler;
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
use App\Entity\CategorySection;

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
     * @var FilesRepository
     */
    private $fileRepo;

    /**
     * @var TagAwareQueryResultCacheCategoryConf
     */
    private $tagAwareQueryResultCacheCategoryConf;

    /**
     * @var TagAwareQuerySecondLevelCacheCategory
     */
    private $tagAwareQuerySecondLevelCacheCategory;

    /**
     * @var TagAwareQueryResultCacheCategory
     */
    private $tagAwareQueryResultCacheCategory;

    /**
     * @var ObjectsHandler
     */
    private $objectsHandler;

    /**
     * @var CategorySectionRepository
     */
    private $categorySectionRepository;

    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * HoverMenuManagmentController constructor.
     * @param CategoryConfigurationsRepository $categoryConfRepo
     * @param CategoryRepository $categoryRepo
     * @param FilesRepository $fileRepo
     * @param TagAwareQueryResultCacheCategoryConf $tagAwareQueryResultCacheCategoryConf
     * @param TagAwareQuerySecondLevelCacheCategory $tagAwareQuerySecondLevelCacheCategory
     * @param ObjectsHandler $objectsHandler
     * @param TagAwareQueryResultCacheCategory $tagAwareQueryResultCacheCategory
     * @param CategorySectionRepository $categorySectionRepository
     * @param CategoryService $categoryService
     */
    public function __construct(
        CategoryConfigurationsRepository $categoryConfRepo,
        CategoryRepository $categoryRepo,
        FilesRepository $fileRepo,
        Helpers $helpers,
        TagAwareQueryResultCacheCategoryConf $tagAwareQueryResultCacheCategoryConf,
        TagAwareQuerySecondLevelCacheCategory $tagAwareQuerySecondLevelCacheCategory,
        ObjectsHandler $objectsHandler,
        TagAwareQueryResultCacheCategory $tagAwareQueryResultCacheCategory,
        CategorySectionRepository $categorySectionRepository,
        CategoryService $categoryService
    )
    {
        parent::__construct($helpers);

        $this->categoryConfRepo = $categoryConfRepo;
        $this->categoryRepo = $categoryRepo;
        $this->fileRepo = $fileRepo;
        $this->tagAwareQueryResultCacheCategoryConf = $tagAwareQueryResultCacheCategoryConf;
        $this->tagAwareQuerySecondLevelCacheCategory = $tagAwareQuerySecondLevelCacheCategory;
        $this->objectsHandler = $objectsHandler;
        $this->tagAwareQueryResultCacheCategory = $tagAwareQueryResultCacheCategory;
        $this->categorySectionRepository = $categorySectionRepository;
        $this->categoryService = $categoryService;
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
     * @return array
     * @throws DBALException
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function editHoverMenuAction(Request $request)
    {
        /** @var Registry $registry */
        $registry = $this->get('doctrine');
        $objectManager = $registry->getManager();
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $objectManager->getConnection();
        $connection->beginTransaction(); // suspend auto-commit
        try{
            if (!(int)$request->get('category_id')
            ) {
                $category = new Category();
                $category
                    ->setCustomeCategory(true);

                $categoryConfigurations = new CategoryConfigurations();
                $category
                    ->setCategoryConfigurations($categoryConfigurations);

                $matchExistCategory = $this->categoryService
                    ->matchExistCategory($request->get('category_name'));
                if ($matchExistCategory) {
                    throw new \Exception('catefory already exsit');
                }

            } else {
                $categoryConfigurations = $this->categoryConfRepo
                    ->findOneBy(['categoryId' => $request->get('category_id')]);
                $category = $categoryConfigurations->getCategoryId();
            }
            if ($request->get('sections_list')) {
                $categorySection = $this->getCategorySectionRepository()
                    ->findOneBy(['id' => $request->get('sections_list')]);
                if ($categorySection) {
                    $category->setSectionRelation($categorySection);
                }
            }

            if ($request->get('category_name')) {
                $category->setCategoryName($request->get('category_name'));
            }

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

            $category->setSeoTitle($request->get('category_seo_title'));
            $category->setSeoDescription($request->get('category_seo_description'));
            $category->setSeoText1($request->get('category_seo_text1'));
            $category->setSeoText2($request->get('category_seo_text2'));
            if ($request->get('hotCatgory')) {
                $category->setHotCategory(true);
            } else {
                $category->setHotCategory(false);
            }
            $this->objectsHandler
                ->validateEntity(
                    $category,
                    [Category::SERIALIZED_GROUP_CREATE]
                );

            $this->categoryRepo->getPersist($category);
            $this->categoryConfRepo->getPersist($categoryConfigurations);

            if (!(int)$request->get('category_id')
                && (int)$request->get('main_category_id')) {
                $main = $this->categoryRepo
                    ->findOneBy(['id' => $request->get('main_category_id')]);
                if ($main) {
                    $categoryRelations = new CategoryRelations();
                    $categoryRelations
                        ->setSubCategory($category)
                        ->setMainCategory($main);

                    $this->categoryRepo
                        ->getPersist($categoryRelations);
                }
            }

            $this->categoryRepo->getPersist($categoryConfigurations);
            $fileIds = $request->get('file_ids');
            if (is_array($fileIds) && count($fileIds)) {
                $files = $this->fileRepo
                    ->getByIds($fileIds);
                foreach ($files as $file) {
                    $file->setCategory($category);
                }
            }
            $objectManager->flush();
            $connection->commit();
            if ($request->get('disableForParsing')) {
                if (!$category->isDisableForParsing()) {
                    $category->setDisableForParsing(true);
                    $this->setUpdateDisableForParsingByIds($category, true);
                }
            } else {
                if ($category->isDisableForParsing()) {
                    $category->setDisableForParsing(false);
                    $this->setUpdateDisableForParsingByIds($category, false);
                }
            }

            /** @fileIds Configuration $configuration */
            $configuration = $objectManager->getConfiguration();
            /** @fileIds DoctrineProvider $resultCacheImpl1 */
            $resultCacheImpl1 = $configuration->getResultCacheImpl();
            $resultCacheImpl1->delete(CategoryRepository::CACHE_HOT_CATEGORY_ID);
            $resultCacheImpl1->delete(CategoryRepository::CACHE_CUSTOM_CATEGORY_ID);

            $this->getTagAwareQueryResultCacheCategoryConf()
                ->getTagAwareAdapter()
                ->invalidateTags([
                    CategoryConfigurationsRepository::CATEGORY_CONF_SEARCH,
                    CategoryConfigurationsRepository::CATEGORY_CONF_SEARCH_SUB_COUNT
                ]);

            $this->getTagAwareQueryResultCacheCategory()
                ->getTagAwareAdapter()
                ->invalidateTags([
                    CategoryRepository::MAIN_CATEGORY_IDS,
                    CategoryRepository::SUB_CATEGORIES,
                    CategoryRepository::CATEGORY_FACET_FILTER,
                ]);

            $this->getTagAwareQueryResultCacheCategory()
                ->delete(CategoryRepository::MAIN_CATEGORY_IDS_DATA);

            $this->tagAwareQuerySecondLevelCacheCategory
                ->deleteAll();
        }catch(\Exception $exception){
            $connection->rollBack();
            throw $exception;
        }


        return ['success' => 1];
    }

    /**
     * get sections list.
     *
     * @Rest\Get("/api/hover_menu/sections_list", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK, serializerGroups={CategorySection::SERIALIZED_GROUP_LIST})
     *
     * @SWG\Tag(name="HoverMenu")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return array
     * @throws DBALException
     * @throws ValidatorException
     */
    public function getSectionListAction(Request $request)
    {
        $sections = $this->getCategorySectionRepository()
            ->getSections();
        return $sections;
    }

    /**
     * get th.
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
     * @return TagAwareQueryResultCacheCategory
     */
    public function getTagAwareQueryResultCacheCategory(): TagAwareQueryResultCacheCategory
    {
        return $this->tagAwareQueryResultCacheCategory;
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

    /**
     * @return CategorySectionRepository
     */
    private function getCategorySectionRepository(): CategorySectionRepository
    {
        return $this->categorySectionRepository;
    }
}