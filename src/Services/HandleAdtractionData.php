<?php

namespace App\Services;

use App\Kernel;
use App\QueueModel\AdtractionDataRow;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\TraceableMessageBus;
use function League\Csv\delimiter_detect;

class HandleAdtractionData
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var TraceableMessageBus
     */
    private $bus;

    /**
     * HandleAdtractionData constructor.
     */
    public function __construct(
        KernelInterface $kernel,
        MessageBusInterface $bus
    )
    {
        $this->kernel = $kernel;
        $this->bus = $bus;
    }

    /**
     * @param $filePath
     * @throws \League\Csv\Exception
     * @throws \Throwable
     */
    public function parseCSVContent($filePath)
    {
        /** @var Reader $csv */
        $csv = Reader::createFromPath($filePath, 'r');
//        $csv->isActiveStreamFilter(); //return true

//        $csv->setDelimiter(',');
        $csv->setHeaderOffset(0);
//        $csv->setEscape('\'');
        $csv->setEnclosure('\'');

        $result = delimiter_detect($csv, [','], 10);
        $count = $result[','];
        $offset = 0;
        while ($offset < $count) {

            //build a statement
            $stmt = (new Statement())
                ->offset($offset)
                ->limit(1);

            //query your records from the document
            $records = $stmt->process($csv);
            $header = $csv->getHeader();
            foreach ($records as $record) {
                $unserializeRecord = serialize($record);
                $this->getBus()->dispatch(new AdtractionDataRow($unserializeRecord));

            }

            $offset += 1;
        }
    }

    /**
     * @return Kernel
     */
    public function getKernel(): Kernel
    {
        return $this->kernel;
    }

    /**
     * @return TraceableMessageBus
     */
    public function getBus(): TraceableMessageBus
    {
        return $this->bus;
    }
}