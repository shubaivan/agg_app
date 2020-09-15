<?php


namespace App\Services\Admin;


use App\Entity\Collection\Admin\ShopRules\EditShopRules;
use App\Repository\AdminShopsRulesRepository;

class AdminShopRules
{
    /**
     * @var AdminShopsRulesRepository
     */
    private $srr;

    /**
     * AdminShopRules constructor.
     * @param AdminShopsRulesRepository $srr
     */
    public function __construct(AdminShopsRulesRepository $srr)
    {
        $this->srr = $srr;
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
}