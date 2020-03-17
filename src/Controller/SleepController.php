<?php

# project/src/Controller/SleepController.php

namespace App\Controller;

use App\QueueModel\FileReadyDownloaded;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class SleepController extends AbstractController
{
    /**
     * @Route(name="sleep", path="sleep")
     */
    public function processVideo(MessageBusInterface $bus) {
        $bus->dispatch(new FileReadyDownloaded(10, 'Hello World'));
        return new Response('<html><body>OK.</body></html>');
    }
}
