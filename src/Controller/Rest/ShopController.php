<?php

namespace App\Controller\Rest;

use App\Entity\Collection\ShopsCollection;
use App\Entity\Shop;
use App\Repository\ShopRepository;
use App\Services\Helpers;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

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
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View(serializerGroups={Shop::SERIALIZED_GROUP_LIST}, statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Shop")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object Shop",
     *     @SWG\Schema(ref=@Model(type=ShopsCollection::class, groups={Shop::SERIALIZED_GROUP_LIST}))
     * )
     *
     * @return ShopsCollection
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getShopsAction(ParamFetcher $paramFetcher)
    {
        $collection = $this->getShopRepository()->getEntityList($paramFetcher);
        $count = $this->getShopRepository()->getEntityList($paramFetcher, true);

        return (new ShopsCollection($collection, $count));
    }

    /**
     * @return ShopRepository
     */
    public function getShopRepository(): ShopRepository
    {
        return $this->shopRepository;
    }
}