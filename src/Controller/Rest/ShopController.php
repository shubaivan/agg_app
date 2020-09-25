<?php

namespace App\Controller\Rest;

use App\Entity\Shop;
use App\Services\Helpers;
use App\Services\Models\ShopService;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use App\Validation\Constraints\SearchQueryParam;
use App\Entity\Collection\ShopsCollection;

class ShopController extends AbstractRestController
{
    /**
     * @var ShopService
     */
    private $shopService;

    /**
     * ShopController constructor.
     * @param ShopService $shopService
     */
    public function __construct(
        ShopService $shopService,
        Helpers $helpers
    )
    {
        parent::__construct($helpers);
        $this->shopService = $shopService;
    }

    /**
     * get Shops.
     *
     * @Rest\Get("/api/shops")
     *
     * @Rest\QueryParam(
     *     name="search",
     *     strict=true,
     *     requirements=@SearchQueryParam,
     *     nullable=true,
     *     description="Search by each sentence/world separatly delimetery which eqaul ',', with `or` condition by sku, name, description, category, brand, shop and price fields")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Shop")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection objects",
     *     @SWG\Schema(
     *         type="object",
     *         properties={
     *             @SWG\Property(
     *                  property="collection",
     *                  type="array",
     *                  @SWG\Items(
     *                        type="object",
     *                      @SWG\Property(property="id", type="integer"),
     *                      @SWG\Property(property="name", type="string"),
     *                      @SWG\Property(property="createdAt", type="string")
     *                  )
     *             ),
     *             @SWG\Property(property="count", type="integer")
     *         }
     *     )
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws DBALException
     * @throws \Exception
     */
    public function getShopsAction(ParamFetcher $paramFetcher)
    {
        $shopsCollection = $this->getShopService()->getShopsByFilter($paramFetcher);
        $view = $this->createSuccessResponse($shopsCollection);
        $view
            ->getResponse()
            ->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }

    /**
     * get Shop by slug.
     *
     * @Rest\Get("/api/shop/{slug}")
     *
     * @SWG\Tag(name="Shop")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json object with relation items",
     *     @Model(type=Shop::class, groups={Shop::SERIALIZED_GROUP_LIST}))
     *     )
     * )
     *
     * @param Shop $shop
     *
     * @return \FOS\RestBundle\View\View
     *
     * @throws \Exception
     */
    public function getShopByIdAction(
        Shop $shop
    )
    {
        $view = $this->createSuccessResponse($shop, [Shop::SERIALIZED_GROUP_LIST]);
        $view
            ->getResponse()
            ->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }

    /**
     * get Shop Facet filters.
     *
     * @Rest\Get("/api/shop/facet_filters/{uniqIdentificationQuery}")
     *
     * @Rest\QueryParam(
     *     name="search",
     *     strict=true,
     *     requirements=@SearchQueryParam,
     *     nullable=true,
     *     description="Search by each sentence/world separatly delimetery which eqaul ',', with `or` condition by shop_name fields")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Shop")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object"
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws \Exception
     */
    public function getShopFacetFiltersAction(ParamFetcher $paramFetcher, $uniqIdentificationQuery)
    {
        $brandsCollection = $this->getShopService()
            ->facetFilters($uniqIdentificationQuery, $paramFetcher);
        $view = $this->createSuccessResponse($brandsCollection);
        $view
            ->getResponse()
            ->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }

    /**
     * get Shops by ids.
     *
     * @Rest\Get("/api/shops/by/ids")
     *
     * @SWG\Tag(name="Shop")
     *
     * @Rest\QueryParam(map=true, name="ids", nullable=false, strict=true, requirements="\d+", default="0", description="List products by ids")
     *
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     *     @SWG\Schema(ref=@Model(type=ShopsCollection::class, groups={Shop::SERIALIZED_GROUP_LIST}))
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function getShopsByIdsAction(ParamFetcher $paramFetcher)
    {
        $productsCollection = $this->getShopService()
            ->getShopsByIds($paramFetcher);
        $view = $this->createSuccessResponse(
            $productsCollection, [Shop::SERIALIZED_GROUP_LIST]
        );
        $view->getResponse()->setExpires($this->getHelpers()->getExpiresHttpCache());

        return $view;
    }

    /**
     * @return ShopService
     */
    public function getShopService(): ShopService
    {
        return $this->shopService;
    }
}