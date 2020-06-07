<?php

namespace App\EventListener;

use App\QueueModel\ResourceDataRow;
use App\Services\Queue\ProductDataRowHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;

class MessengerListener implements EventSubscriberInterface
{
    /**
     * @var ProductDataRowHandler
     */
    private $productDataRowHandler;

    /**
     * MessengerListener constructor.
     * @param ProductDataRowHandler $productDataRowHandler
     */
    public function __construct(ProductDataRowHandler $productDataRowHandler)
    {
        $this->productDataRowHandler = $productDataRowHandler;
    }

    /**
     * @param WorkerMessageHandledEvent $event
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Throwable
     */
    public function onHandled(WorkerMessageHandledEvent $event)
    {
        $envelope = $event->getEnvelope();
        $message = $envelope->getMessage();
        if ($message instanceof ResourceDataRow) {
            $this->getProductDataRowHandler()
                ->handleAnalysisProductByMainCategory($message);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageHandledEvent::class => 'onHandled'
        ];
    }

    /**
     * @return ProductDataRowHandler
     */
    private function getProductDataRowHandler(): ProductDataRowHandler
    {
        return $this->productDataRowHandler;
    }
}