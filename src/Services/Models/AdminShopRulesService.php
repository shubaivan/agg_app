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
            } else {
                $columnConf[$column] = [$keyWords];
            }

        }
        $identityColumns = false;
        foreach ($columnConf as $column=>$rule) {
            $value = $propertyAccessor->getValue($product, $column);
            if ($value) {
                $identityColumns = true;
                if (is_array($rule)) {
                    foreach ($rule as $extraKey=>$extraRule) {
                        if (isset($value[$extraKey])) {
                            $implode = implode('|', $extraRule);

                            if (preg_match_all("/$implode/iu", $value[$extraKey], $mt)) {
                                $failedRule = false;
                                break;
                            } else {
                                $failedRule = true;
                            }
                        }
                    }
                    if (isset($failedRule) && !$failedRule) {
                        break;
                    }
                } else {
                    $implode = implode('|', $rule);
                }
                
                if (preg_match_all("/$implode/iu", $value, $mt)) {
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