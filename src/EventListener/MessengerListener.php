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
        $this->getLogger()->debug('Message dispatched: '
            . (get_class($event->getEnvelope()->getMessage()) ?? null));
    }

    public function onFailed(WorkerMessageFailedEvent $event)
    {
        if ($event->getThrowable()) {
            $this->getLogger()->error('Message failed'
                . $event->getThrowable()->getMessage());
        }

        if ($event->willRetry()) {
            $this->getLogger()->debug('Message will be retried'
                . ($event->getThrowable()->getMessage() ?? null));
        } else {
            $this->getLogger()->debug('Message will not be retried'
                . ($event->getThrowable()->getMessage() ?? null));
        }
    }

    public function onReceived(WorkerMessageReceivedEvent $event)
    {
        if ($event->getEnvelope()) {
            $this->getLogger()->debug('Received message '
                . (get_class($event->getEnvelope()->getMessage()) ?? null));
        }
    }

    public function onHandled(WorkerMessageHandledEvent $event)
    {
        $this->getLogger()->debug('Task handled by receiver name'
            . $event->getReceiverName() . ' message: '
            . (get_class($event->getEnvelope()->getMessage() ?? null)));
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
