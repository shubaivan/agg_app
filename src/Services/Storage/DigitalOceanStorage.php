<?php


namespace App\Services\Storage;

use Keven\Flysystem\Concatenate\Append;
use Keven\Flysystem\Concatenate\Concatenate;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Util;

class DigitalOceanStorage
{
    private $storage;

    // The variable name $defaultStorage matters: it needs to be the camelized version
    // of the name of your storage.
    public function __construct(FilesystemInterface $reaourcesStorage)
    {
        $reaourcesStorage->addPlugin(new Append());
        $this->storage = $reaourcesStorage;
    }

    public function appendOrCreate(string $path, string $content)
    {
        $path = Util::normalizePath($path);
        if ($this->getStorage()->has($path)) {
            $this->getStorage()->append($path, $content);
        } else {
            $this->getStorage()->write($path, $content, ['visibility' => 'public']);
        }

        echo $this->getStorage()->read($path); // file1more
    }

    public function generateTimeStamp()
    {
        return (new \DateTime())->format('Ymd_H:i:s');
    }

    public function generateUniqId()
    {
        return md5(uniqid());
    }

    /**
     * @return FilesystemInterface
     */
    public function getStorage(): FilesystemInterface
    {
        return $this->storage;
    }
}