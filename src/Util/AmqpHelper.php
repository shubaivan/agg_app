<?php


namespace App\Util;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class AmqpHelper
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * AmqpHelper constructor.
     * @param string $name
     * @param string $password
     * @param string $host
     * @param string $port
     */
    public function __construct(string $name, string $password, string $host, string $port)
    {
        $this->name = $name;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @return array
     */
    public function getQuantityJobsQueue(string $queueName)
    {
        $connection = new AMQPStreamConnection(
            $this->host,
            $this->port,
            $this->name,
            $this->password
        );
        $channel = $connection->channel();
        list($queue, $messageCount, $consumerCount) = $channel
            ->queue_declare($queueName, true);

        return [
            'messageCount' => $messageCount,
            'queue' => $queue,
            'consumerCount' => $consumerCount,
        ];
    }
}