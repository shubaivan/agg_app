<?php

namespace App\QueueModel;

class AdtractionDataRow extends ResourceProductQueues
{
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
}
