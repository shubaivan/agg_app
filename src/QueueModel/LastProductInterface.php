<?php

namespace App\QueueModel;

interface LastProductInterface
{
    /**
     * @return bool
     */
    public function getLastProduct(): bool;

    /**
     * @param bool $isEndProduct
     * @return $this
     */
    public function setLastProduct(bool $isEndProduct);
}
