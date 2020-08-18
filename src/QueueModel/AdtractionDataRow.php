<?php

namespace App\QueueModel;

class AdtractionDataRow extends ResourceProductQueues implements ResourceDataRow
{
    public function getName()
    {
        return isset($this->row['Name']) ?? null;
    }
}
