<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class JollyRoomService implements IdentityGroup
{
    /**
     * name: "Paw Patrol Baddräkt, Lila, 6 År"
     *
     * @param Product $product
     * @return mixed|void
     */
    public function identityGroupColumn(Product $product)
    {
        $name = $product->getName();
        $explodeName = explode(',', $name);
        if (count($explodeName) > 0) {
            $groupIdentity = str_replace(' ', '_', mb_strtolower(array_shift($explodeName)));
            $product->setGroupIdentity($groupIdentity);
            if (count($explodeName) > 0) {
                $color = trim(array_shift($explodeName));
                $product->setSeparateExtra('COLOUR', $color);
                if (count($explodeName) > 0) {
                    $ageGroup = trim(array_shift($explodeName));
                    $product->setSeparateExtra('AGE_GROUP', $ageGroup);
                }
            }
        }
    }
}