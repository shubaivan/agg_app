<?php


namespace App\Services\Models\Shops;

use App\Entity\Product;

interface IdentityGroup
{
    /**
     * @param Product $product
     * @return mixed
     */
    public function identityGroupColumn(Product $product);

    public function identityBrand();
}