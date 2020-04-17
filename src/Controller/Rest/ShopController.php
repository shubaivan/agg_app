<?php

namespace App\Controller\Rest;

use App\Entity\Collection\ShopsCollection;
use App\Entity\Shop;
use App\Repository\ShopRepository;
use App\Services\Helpers;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use App\Validation\Constraints\SearchQueryParam;

class ShopController extends AbstractRestController
{
    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * ShopController constructor.
     * @param ShopRepository $shopRepository
     */
    public function __construct(
        ShopRepository $shopRepository,
        Helpers $helpers
    )
    {
        parent::__construct($helpers);
        $this->shopRepository = $shopRepository;
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
     * @return ShopsCollection
     * @throws DBALException
     */
    public function getShopsAction(ParamFetcher $paramFetcher)
    {
        $collection = $this->getShopRepository()->fullTextSearchByParameterFetcher($paramFetcher);
        $count = $this->getShopRepository()->fullTextSearchByParameterFetcher($paramFetcher, true);

        return (new ShopsCollection($collection, $count));
    }

    /**
     * get Shop by id.
     *
     * @Rest\Get("/api/shop/{id}", requirements={"id"="\d+"})
     *
     * @View(serializerGroups={Shop::SERIALIZED_GROUP_LIST}, statusCode=Response::HTTP_OK)
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
     * @return Shop
     */
    public function getCategoryByIdAction(
        Shop $shop
    )
    {
        return $shop;
    }

    /**
     * @return ShopRepository
     */
    public function getShopRepository(): ShopRepository
    {
        return $this->shopRepository;
    }
}