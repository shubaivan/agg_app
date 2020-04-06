<?php

namespace App\Controller;

use App\Entity\Product;
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
        /** @var \Doctrine\Bundle\DoctrineBundle\Registry $obj */
        $obj = $this->get('doctrine');
        $objectManager = $obj->getManager();

        $product = $objectManager->getRepository(Product::class)
            ->fff();

        /** @var Product $product */
        $product = $objectManager->getRepository(Product::class)
            ->findOneBy(['id' => 14661]);
        $product
            ->setExtras(['test' => 'result', 'hello' => 'world']);
        $objectManager->flush();
        return new Response('<html><body>' . ($path ?? 'OK') . '</body></html>');
    }
}
