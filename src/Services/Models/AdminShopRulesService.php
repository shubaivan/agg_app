<?php


namespace App\Services\Models;


use App\Entity\Product;
use App\Exception\AdminShopRulesException;
use App\Repository\AdminShopsRulesRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AdminShopRulesService
{
    /**
     * @var AdminShopsRulesRepository
     */
    private $repo;

    /**
     * AdminShopRulesService constructor.
     * @param AdminShopsRulesRepository $repo
     */
    public function __construct(AdminShopsRulesRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Product $product
     * @return array
     * @throws AdminShopRulesException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function executeShopRule(Product $product) {
        if (!strlen($product->getShop())) {
            return [];
        }
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $adminShopsRules = $this->repo->findConfByStore($product->getShop());
        if (!count($adminShopsRules)) {
            return [];
        }
        $columnConf = [];
        foreach ($adminShopsRules as $column=>$keyWords) {
            if (is_array($keyWords)) {
                $columnConf[$column] = array_unique($keyWords);
            }

        }
        $identityColumns = false;
        foreach ($columnConf as $column=>$rule) {
            $implode = implode('|', $rule);
            $value = $propertyAccessor->getValue($product, $column);
            if ($value) {
                $identityColumns = true;
                if (preg_match_all("/$implode/u", $value, $mt)) {
                    $failedRule = false;
                    break;
                } else {
                    $failedRule = true;
                }
            }
        }

        if ($identityColumns && isset($failedRule) && $failedRule) {
            throw new AdminShopRulesException();
        }

        return [];
    }
}