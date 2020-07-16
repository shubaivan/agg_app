<?php


namespace App\Services\Models\Shops;


use App\Entity\Product;

class JollyRoomService extends AbstractShop implements IdentityGroup
{
    /**
     * name: "Paw Patrol Baddräkt, Lila, 6 År"
     * https://www.jollyroom.se/sport/sportskor/traningsskor/adidas-adizero-club-jr-tennisskor
     * https://www.jollyroom.se/sport/sportskor/traningsskor/adidas-adizero-club-jr-tennisskor
     *
     * @param Product $product
     * @return mixed|void
     */
    public function identityGroupColumn(Product $product)
    {
        $name = $product->getName();
        $explodeName = explode(',', $name);
        if (count($explodeName) > 1) {
            $groupIdentity = str_replace(' ', '_', mb_strtolower(array_shift($explodeName)));
            $product->setGroupIdentity($groupIdentity);
            if (count($explodeName) > 0) {
                $color = trim(array_shift($explodeName));
                if (preg_match_all('/[^\d]+/',$color,$matches)) {
                    $separateColor = array_shift($matches);
                    if (is_array($separateColor)) {
                        $separateColor = array_shift($separateColor);
                    }
                    if (mb_check_encoding($separateColor, "UTF-8")) {
                        $this->analysisColorValue($separateColor, $product);
                    }
                }
                if (count($explodeName) > 0) {
                    $ageGroup = trim(array_shift($explodeName));
                    $product->setSeparateExtra('AGE_GROUP', $ageGroup);
                }
            }
        } else {
            if (preg_match('/([^\/]+$)/', $product->getProductUrl(), $matches)) {
                $lastPartUrl = array_shift($matches);
                $product->setGroupIdentity($lastPartUrl);
            }
        }
    }
}