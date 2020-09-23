<?php


namespace App\EventListener\Doctrine;

use App\Entity\SlugAbstract;
use App\EventListener\SlugApproach;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class PgDataBaseActivitySubscriber extends SlugApproach implements EventSubscriber
{
//    private $en;

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::onFlush,
            Events::postFlush,
        ];
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
//        $uow = $em->getUnitOfWork();
//        $uow->getEntityChangeSet($this->en);
    }

    public function onFlush(OnFlushEventArgs $event)
    {
        $object = $event;
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $entityChangeSet = $uow->getEntityChangeSet($entity);
        }
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        $object = $event->getObject();
//        $this->en = $object;
        $this->applySlug($object);
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        $object = $event->getObject();
//        $this->en = $object;
        $this->applySlug($object);
    }

    /**
     * @param object $object
     */
    private function applySlug(object $object): void
    {
        if ($object instanceof SlugAbstract) {
            $this->applySlugToEntity($object);
        }
    }
}