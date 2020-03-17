<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogConsoleCommandListener implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LogListener constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $consoleCommandLogLogger)
    {
        $this->logger = $consoleCommandLogLogger;
    }

    public function terminate(ConsoleTerminateEvent $event)
    {
        $this->getLogger()->info($event->getCommand()->getName() . ' terminate');
    }

    public function command(ConsoleCommandEvent $event)
    {
        $this->getLogger()->info($event->getCommand()->getName() . ' should run');
    }

    public function error(ConsoleErrorEvent $event)
    {
        $this->getLogger()->error($event->getCommand()->getName() . ': ' . $event->getError()->getMessage());
    }

    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::TERMINATE => 'terminate',
            ConsoleEvents::COMMAND => 'command',
            ConsoleEvents::ERROR => 'error'
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
