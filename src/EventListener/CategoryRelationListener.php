<?php

namespace App\EventListener;

use App\Entity\CategoryRelations;
use App\Entity\Files;
use App\Entity\SlugAbstract;
use App\Services\Storage\DigitalOceanStorage;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CategoryRelationListener extends SlugApproach
{
    /**
     * @param LifecycleEventArgs $args
     * @throws \League\Flysystem\FileExistsException
     * @throws \ReflectionException
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->applySlugApproach($entity);
    }

    /**
     * @param PreUpdateEventArgs $args
     * @throws \League\Flysystem\FileExistsException
     * @throws \ReflectionException
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->applySlugApproach($entity);
    }

    private function applySlugApproach($entity)
    {
        if ($entity instanceof CategoryRelations) {
            $category = $entity->getSubCategory();
            if ($category instanceof SlugAbstract) {
                $this->applySlugToEntity($category);
            }
        }
    }
}
