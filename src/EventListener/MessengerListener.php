<?php

namespace App\EventListener;

use App\QueueModel\FileReadyDownloaded;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;

class MessengerListener implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LogListener constructor.
     *
     * @param LoggerInterface $messengerHandlerLogger
     */
    public function __construct(LoggerInterface $messengerHandlerLogger)
    {
        $this->logger = $messengerHandlerLogger;
    }

    public function onSendToTransport(SendMessageToTransportsEvent $event)
    {
        $this->getLogger()->debug('Message dispatched');
    }

    public function onFailed(WorkerMessageFailedEvent $event)
    {
        if ($event->getThrowable()) {
            $this->getLogger()->error($event->getThrowable()->getMessage());
        }
        $this->getLogger()->debug('Message failed');

        if ($event->willRetry()) {
            $this->getLogger()->debug('Message will be retried');
        } else {
            $this->getLogger()->debug('Message will not be retried');
        }
    }

    public function onReceived(WorkerMessageReceivedEvent $event)
    {
        if ($event->getEnvelope()) {
            $this->getLogger()->debug('Received message ' . get_class($event->getEnvelope()->getMessage()));
        }
    }

    public function onHandled(WorkerMessageHandledEvent $event)
    {
        $this->getLogger()->debug('Task handled');
    }

    public static function getSubscribedEvents()
    {
        return [
            SendMessageToTransportsEvent::class => 'onSendToTransport',
            WorkerMessageReceivedEvent::class => 'onReceived',
            WorkerMessageFailedEvent::class => 'onFailed',
            WorkerMessageHandledEvent::class => 'onHandled'
        ];
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
