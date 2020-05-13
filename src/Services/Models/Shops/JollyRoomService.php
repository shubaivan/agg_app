<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class JollyRoomService implements IdentityGroup
{
    /**
     * @param Product $product
     * @return mixed|void
     */
    public function identityGroupColumn(Product $product)
    {
        $name = $product->getName();
        $explodeName = explode(',', $name);
        if (count($explodeName) > 1) {
            $product->setGroupIdentity(array_shift($explodeName));
            if (count($explodeName) > 1) {
                $product->setExtras(array_merge($product->getExtras(), ['COLOUR' => array_shift($explodeName)]));
                if (count($explodeName) > 1) {
                    $product->setExtras(array_merge($product->getExtras(), ['AGE_GROUP' => array_shift($explodeName)]));
                }
            }
        }
    }
}