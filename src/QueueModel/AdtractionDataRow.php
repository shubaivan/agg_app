<?php

# project/src/QueueModel/FileReadyDownloaded.phpded.php

namespace App\QueueModel;

class AdtractionDataRow
{
    /**
     * @var string
     */
    private $row;

    /**
     * AdtractionDataRow constructor.
     * @param string $row
     */
    public function __construct(string $row)
    {
        $this->row = $row;
    }

    /**
     * @return string
     */
    public function getRow(): string
    {
        return $this->row;
    }
}
