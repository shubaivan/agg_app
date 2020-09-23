<?php


namespace App\EventListener\Doctrine;

use Doctrine\Bundle\MongoDBBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;

class DatabaseActivitySubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
        ];
    }

    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        $documentManager = $eventArgs->getDocumentManager();
    }
}