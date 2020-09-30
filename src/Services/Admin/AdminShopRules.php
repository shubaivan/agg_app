<?php


namespace App\Services\Admin;


use App\Entity\AdminShopsRules;
use App\Entity\Collection\Admin\ShopRules\CreateShopRules;
use App\Entity\Collection\Admin\ShopRules\EditShopRules;
use App\Entity\Shop;
use App\Repository\AdminShopsRulesRepository;
use App\Repository\ShopRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

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
     * @var EntityManager
     */
    private $em;

    /**
     * AdminShopRules constructor.
     * @param AdminShopsRulesRepository $srr
     * @param ShopRepository $shopRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(
        AdminShopsRulesRepository $srr,
        ShopRepository $shopRepository,
        EntityManagerInterface $em
    )
    {
        $this->srr = $srr;
        $this->sr = $shopRepository;
        $this->em = $em;
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

        $this->clearCacheForShopRules($adminShopsRules);
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
        $this->clearCacheForShopRules($adminShopsRulesNew);
    }

    /**
     * @return EntityManager
     */
    private function getEm(): EntityManager
    {
        return $this->em;
    }

    /**
     * @param AdminShopsRules $adminShopsRules
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function clearCacheForShopRules(AdminShopsRules $adminShopsRules): void
    {
        $this->getEm()->getConfiguration()->getResultCacheImpl()
            ->delete(AdminShopsRulesRepository::SHOP_RULE_LIST_BY_SHOP . $adminShopsRules->getStore());

        $this->srr
            ->getTagAwareQueryResultCacheShop()
            ->getTagAwareAdapter()
            ->invalidateTags([AdminShopsRulesRepository::DATA_TABLES]);

        $this->srr
            ->getTagAwareQueryResultCacheShop()
            ->delete($adminShopsRules->getStore());
    }
}