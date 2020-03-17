<?php

namespace App\QueueModelHandlers;

use App\QueueModel\AdtractionDataRow;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AdtractionDataRowHandler implements MessageHandlerInterface
{
    /**
     * @param AdtractionDataRow $adtractionDataRow
     */
    public function __invoke(AdtractionDataRow $adtractionDataRow)
    {
        $row = $adtractionDataRow->getRow();

        echo $row . PHP_EOL;
    }
}
