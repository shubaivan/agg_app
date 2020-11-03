<?php


namespace App\Services\Models\Shops;

use App\Entity\BrandStrategy;
use App\Entity\Product;
use App\Services\Models\StrategyService;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class AbstractShop implements IdentityGroup
{
    const SIZE_TWO_CHARACTER = '\b([A-Z]{2})\b';
    const SIZE_ONE_CHARACTER = '\b([A-Z]{1})\b';

    /**
     * @var StrategyService
     */
    protected $strategyService;

    /**
     * @var array
     */
    protected $identityBrand = [];

    /**
     * AbstractShop constructor.
     * @param StrategyService $strategyService
     */
    public function __construct(StrategyService $strategyService)
    {
        $this->strategyService = $strategyService;
        $this->identityBrand = $this->identityBrand();
    }

    public function identityBrand()
    {
        return [];
    }


    protected function analysisColorValue(string $color, Product $product)
    {
        $one = self::SIZE_ONE_CHARACTER;
        $two = self::SIZE_TWO_CHARACTER;
        if (!preg_match_all('/[0-9]+/', $color, $matchesD)
            && !preg_match("/$one/", $color, $foundOne)
            && !preg_match("/$two/", $color, $foundTwo)
        ) {
            $color = str_replace('(', '', $color);
            $color = str_replace(')', '', $color);
            $product->setSeparateExtra(Product::COLOUR, $color);
        }

        if (preg_match('/[0-9]+/', $color, $matchesD)
        ) {
            if (count($matchesD)) {
                $exactlySize = array_shift($matchesD);
                $product->setSeparateExtra(Product::SIZE, $exactlySize);
            }
        }

        if (preg_match_all("/$one/", $color, $foundOne)) {
            $value = array_shift($foundOne);
            if (is_array($value)) {
                $value = array_shift($value);
                $product->setSeparateExtra(Product::SIZE, $value);
            }
        }

        if (preg_match_all("/$two/", $color, $foundTwo)
        ) {
            $value = array_shift($foundTwo);
            if (is_array($value)) {
                $value = array_shift($value);
                $product->setSeparateExtra(Product::SIZE, $value);
            }
        }
    }

    /**
     * @param Product $product
     * @return bool|mixed
     * @throws \ReflectionException
     */
    public function identityGroupColumn(Product $product)
    {
        $brand = $product->getBrandRelation();
        if ($brand && $brand->getBrandStrategies()->count()) {
            $collectionStrategy = $brand->getBrandStrategies()
                ->filter(function ($v) use ($product) {
                    /** @var $v BrandStrategy */
                    return $v->getShop()->getSlug() === $product->getShopRelation()->getSlug();
                });
            if ($collectionStrategy->count()) {
                /** @var BrandStrategy $brandStrategyDb */
                $brandStrategyDb = $collectionStrategy->first();
                $strategy = $this->strategyService
                    ->prepareStrategyInstanceWithArgs(
                        $brandStrategyDb->getStrategy(),
                        (new ParameterBag($brandStrategyDb->getRequiredArgs()))
                    );
                $strategy($product);

                if ($product->getGroupIdentity()) {
                    return true;
                }
            }
        }

        return false;
    }
}