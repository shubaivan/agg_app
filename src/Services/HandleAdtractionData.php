<?php

namespace App\Services;

use App\QueueModel\AdtractionDataRow;
use League\Csv\Reader;
use League\Csv\Statement;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\TraceableMessageBus;
use function League\Csv\delimiter_detect;

class HandleAdtractionData
{
    /**
     * @var TraceableMessageBus
     */
    private $bus;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * HandleAdtractionData constructor.
     * @param MessageBusInterface $bus
     */
    public function __construct(
        MessageBusInterface $bus,
        LoggerInterface $adtractionFileHandlerLogger
    )
    {
        $this->bus = $bus;
        $this->logger = $adtractionFileHandlerLogger;
    }

    /**
     * @param string $filePath
     * @param string|null $shop
     * @throws \League\Csv\Exception
     * @throws \Throwable
     */
    public function parseCSVContent(string $filePath, ?string $shop)
    {
        if (!file_exists($filePath)) {
            $this->getLogger()->error('file ' . $filePath . ' no exist');
            throw new \Exception('file ' . $filePath . ' no exist');
        }
        /** @var Reader $csv */
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);
        $csv->setEnclosure('\'');

        $result = delimiter_detect($csv, [','], 10);
        $count = $result[','];
        $this->getLogger()->info(
            'file ' . $filePath . ' count row ' . $count
        );
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
                if ($shop) {
                    $record['shop'] = $shop;
                }
                $this->getBus()->dispatch(new AdtractionDataRow($record));
            }

            $offset += 1;
        }
    }

    /**
     * @return TraceableMessageBus
     */
    protected function getBus(): TraceableMessageBus
    {
        return $this->bus;
    }

    /**
     * @return Logger
     */
    protected function getLogger(): Logger
    {
        return $this->logger;
    }
}