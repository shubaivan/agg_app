<?php


namespace App\Middleware;


use App\QueueModel\FileReadyDownloaded;
use App\QueueModel\FileReadyDownloadedFailed;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Transport\AmqpExt\AmqpStamp;

class ReplyMiddleware implements MiddlewareInterface
{

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        return $stack->next()->handle($envelope, $stack);
    }
}