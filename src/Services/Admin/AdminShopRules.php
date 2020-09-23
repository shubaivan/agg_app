<?php


namespace App\Services\Admin;


use App\Entity\AdminShopsRules;
use App\Entity\Collection\Admin\ShopRules\CreateShopRules;
use App\Entity\Collection\Admin\ShopRules\EditShopRules;
use App\Entity\Shop;
use App\Repository\AdminShopsRulesRepository;
use App\Repository\ShopRepository;

class AdminShopRules
{
    /**
     * @var AdminShopsRulesRepository
     */
    private $srr;

    /**
     * @var ShopRepository
     */
    private $sr;

    /**
     * AdminShopRules constructor.
     * @param AdminShopsRulesRepository $srr
     * @param ShopRepository $shopRepository
     */
    public function __construct(AdminShopsRulesRepository $srr, ShopRepository $shopRepository)
    {
        $this->srr = $srr;
        $this->sr = $shopRepository;
    }

    /**
     * @param EditShopRules $editShopRules
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function updateShopRule(EditShopRules $editShopRules)
    {
        $adminShopsRules = $this->srr->findOneBy(['id' => $editShopRules->getShopRuleId()]);
        if (!$adminShopsRules) {
            throw new \Exception('shop rule was not found, id:' . $editShopRules->getShopRuleId());
        }
        $adminShopsRules->setColumnsKeywords($editShopRules->generateColumnsKeywords());

        $this->srr->save($adminShopsRules);
        $this->srr
            ->getTagAwareQueryResultCacheShop()
            ->getTagAwareAdapter()
            ->invalidateTags([AdminShopsRulesRepository::DATA_TABLES]);
    }

    /**
     * @param CreateShopRules $createShopRules
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function createShopRule(CreateShopRules $createShopRules)
    {
        $shop = Shop::getMapShopKeyByOriginalName($createShopRules->getShopName());
        if (!$shop) {
            throw new \Exception('shop was not found, name:' . $createShopRules->getShopName());
        }

        $adminShopsRules = $this->srr->findOneBy(['store' => $createShopRules->getShopName()]);

        if ($adminShopsRules) {
            throw new \Exception('admin shop rule exist, id:' . $adminShopsRules->getId());
        }

        $adminShopsRulesNew = new AdminShopsRules();

        $adminShopsRulesNew
            ->setStore($createShopRules->getShopName())
            ->setColumnsKeywords($createShopRules->generateColumnsKeywords());

        $this->srr->save($adminShopsRulesNew);
        $this->srr
            ->getTagAwareQueryResultCacheShop()
            ->getTagAwareAdapter()
            ->invalidateTags([AdminShopsRulesRepository::DATA_TABLES]);
    }
}