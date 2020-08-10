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
            if (strpos($column, '!') !== false) {
                if (is_array($keyWords)) {
                    $columnConf['negative'][$column] = array_unique($keyWords);
                } else {
                    $columnConf['negative'][$column] = [$keyWords];
                }
            } else {
                if (is_array($keyWords)) {
                    $columnConf['positive'][$column] = array_unique($keyWords);
                } else {
                    $columnConf['positive'][$column] = [$keyWords];
                }
            }

        }

        if (isset($columnConf['positive'])) {
            $identityColumns = false;
            foreach ($columnConf['positive'] as $column=>$rule) {
                $value = $propertyAccessor->getValue($product, $column);
                if ($value) {
                    $identityColumns = true;
                    $extraRue = false;
                    foreach ($rule as $execRule) {
                        if (is_array($execRule)) {$extraRue = true; break;}
                    }
                    if ($extraRue) {
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
                        continue;
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
                throw new AdminShopRulesException('positive key word was not found');
            }
        }

        if (isset($columnConf['negative'])) {
            $identityColumns = false;
            foreach ($columnConf['negative'] as $column=>$rule) {
                $column = preg_replace('/\!/', '', $column);
                $value = $propertyAccessor->getValue($product, $column);
                if ($value) {
                    $identityColumns = true;
                    $extraRue = false;
                    foreach ($rule as $execRule) {
                        if (is_array($execRule)) {$extraRue = true; break;}
                    }
                    if ($extraRue) {
                        foreach ($rule as $extraKey=>$extraRule) {
                            if (isset($value[$extraKey])) {
                                $implode = implode('|', $extraRule);

                                if (preg_match_all("/$implode/iu", $value[$extraKey], $mt)) {
                                    $failedRule = true;
                                    break;
                                } else {
                                    $failedRule = false;
                                }
                            }
                        }
                        if (isset($failedRule) && !$failedRule) {
                            break;
                        }
                        continue;
                    } else {
                        $implode = implode('|', $rule);
                    }

                    if (preg_match_all("/$implode/iu", $value, $mt)) {
                        $failedRule = true;
                        break;
                    } else {
                        $failedRule = false;
                    }
                }
            }

            if ($identityColumns && isset($failedRule) && $failedRule) {
                throw new AdminShopRulesException('
                negative key word was found ' 
                    . (isset($mt) && count($mt) ? implode(',',array_shift($mt)) : ''));
            }
        }

        return [];
    }
}