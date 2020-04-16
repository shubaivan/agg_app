<?php

# project/src/QueueModel/FileReadyDownloaded.phpded.php

namespace App\QueueModel;

class AdtractionDataRow
{
    /**
     * @var array
     */
    private $row;

    /**
     * AdtractionDataRow constructor.
     * @param array $row
     */
    public function __construct(array $row)
    {
        $this->row = $row;
    }

    /**
     * @return array
     */
    public function getRow(): array
    {
        return $this->row;
    }

    /**
     * @return string|null
     */
    public function getShop()
    {
        return $this->row['shop'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getSku()
    {
        return $this->row['SKU'] ?? null;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setExistProductId(int $id)
    {
        if ($this->getRow() && is_array($this->row)) {
            $this->row['id'] = $id;
        }

        return $this;
    }
}
