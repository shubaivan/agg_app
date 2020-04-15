<?php

namespace App\Controller;

use App\Cache\CacheManager;
use App\QueueModel\FileReadyDownloaded;
use App\Services\ObjectsHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class FileDownloadController extends AbstractController
{
    /**
     * @var ObjectsHandler
     */
    private $handler;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * FileDownloadController constructor.
     * @param ObjectsHandler $handler
     * @param CacheManager $cacheManager
     */
    public function __construct(ObjectsHandler $handler, CacheManager $cacheManager)
    {
        $this->handler = $handler;
        $this->cacheManager = $cacheManager;
    }

    /**
     * @Route(name="sleep/{path}", path="sleep")
     */
    public function processVideo(
        MessageBusInterface $bus,
        ?string $path,
        EntityManagerInterface $em
    )
    {
        $this->getCacheManager()->clearAllPoolsCache();
        $articleContent = <<<EOF
**successful** all keay was removed.
EOF;
        if ($path) {
            $fileReadyDownloaded = new FileReadyDownloaded($path, 'test');
            $bus->dispatch($fileReadyDownloaded);
        }
        return new Response('<html><body>' . $articleContent . '</body></html>');
    }

    /**
     * @return ObjectsHandler
     */
    public function getHandler(): ObjectsHandler
    {
        return $this->handler;
    }

    /**
     * @return CacheManager
     */
    public function getCacheManager(): CacheManager
    {
        return $this->cacheManager;
    }
}
