<?php

namespace App\Controller;

use App\QueueModel\FileReadyDownloaded;
use App\Services\ObjectsHandler;
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
     * SleepController constructor.
     * @param ObjectsHandler $handler
     */
    public function __construct(ObjectsHandler $handler)
    {
        $this->handler = $handler;
    }


    /**
     * @Route(name="sleep/{path}", path="sleep")
     */
    public function processVideo(MessageBusInterface $bus, ?string $path)
    {
        if ($path) {
            $fileReadyDownloaded = new FileReadyDownloaded($path);
            $bus->dispatch($fileReadyDownloaded);
        }

        return new Response('<html><body>' . ($path ?? 'OK') . '</body></html>');
    }
}
