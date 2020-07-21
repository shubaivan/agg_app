<?php


namespace App\Services\Models\Shops;

use App\Entity\Product;

abstract class AbstractShop
{
    const SIZE_TWO_CHARACTER = '\b([A-Z]{2})\b';
    const SIZE_ONE_CHARACTER = '\b([A-Z]{1})\b';

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

        if (preg_match_all('/[0-9]+/', $color, $matchesD)
            || preg_match("/$one/", $color, $foundOne)
            || preg_match("/$two/", $color, $foundTwo)
        ) {
            $product->setSeparateExtra(Product::SIZE, $color);
        }
    }
}