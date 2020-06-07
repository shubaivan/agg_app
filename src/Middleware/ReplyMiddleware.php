<?php


namespace App\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * Middleware responsible for replying results returned by handler.
 */
class ReplyMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        return $stack->next()->handle($envelope, $stack);
    }
}