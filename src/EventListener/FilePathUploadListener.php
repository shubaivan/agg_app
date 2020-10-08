<?php

namespace App\EventListener;

use App\Entity\Files;
use App\Services\Storage\DigitalOceanStorage;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilePathUploadListener
{
    /**
     * @var DigitalOceanStorage
     */
    private $uploader;

    public function __construct(DigitalOceanStorage $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \League\Flysystem\FileExistsException
     * @throws \ReflectionException
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    /**
     * @param PreUpdateEventArgs $args
     * @throws \League\Flysystem\FileExistsException
     * @throws \ReflectionException
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    /**
     * @param $entity
     * @throws \League\Flysystem\FileExistsException
     * @throws \ReflectionException
     * @throws \Exception
     */
    private function uploadFile($entity)
    {
        if (!$entity instanceof Files) {
            return;
        }

        $file = $entity->getPath();

        if ($file instanceof UploadedFile) {
            $fileName = $this->uploader->generateTimeStamp();
//            $uniqId = $this->uploader->generateUniqId();
            $ext = $file->guessExtension();
            $commonName = $fileName.'_'.$file->getClientOriginalName();
            $fileSize = $file->getSize();
            switch (true) {
                case (bool)$entity->getBufferEntity():
                    $path = (new \ReflectionClass($entity->getBufferEntity()))->getShortName() .'/' . $entity->getBufferEntity()->getSlug().'/';
                    break;
                default:
                    $path = '';
            }
            $path .= $commonName;
            $has = $this->uploader->getStorage()->has($path);
            if (!$has) {
                $contents = file_get_contents($file->getPathname());
                $write = $this->uploader->getStorage()->write(
                    $path,
                    $contents,
                    ['visibility' => 'public']
                );
                if (!$write) {
                    throw new \Exception('file was not load');
                }
            }
            $entity
                ->setPath($path)
                ->setExtension($ext)
                ->setOriginalName($file->getClientOriginalName())
                ->setSize($fileSize);

        } elseif ($file instanceof File) {
            $entity->setPath($file->getFilename());
        }
    }
}
