<?php

namespace App\QueueModel;

use App\Document\AdtractionProduct;

class AdtractionDataRow extends ResourceProductQueues
{
    protected static $mongoClass = AdtractionProduct::class;

    public function getName()
    {
        return $this->row['Name'] ?? null;
    }

    public function getBrand()
    {
        return $this->row['Brand'] ?? null;
    }

    public function getEan()
    {
        return $this->row['Ean'] ?? null;
    }

    public function generateIdentityUniqData()
    {
        if (isset($this->row['identityUniqData']) && strlen($this->row['identityUniqData'])) {
            return $this->row['identityUniqData'];
        }

        return $this->getSku();
    }
}
