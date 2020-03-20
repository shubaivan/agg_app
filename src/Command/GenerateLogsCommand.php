<?php

namespace App\Command;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateLogsCommand extends Command
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * {@inheritDoc}
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger, ?string $name = null)
    {
        parent::__construct($name);
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('app:generate:logs')
            ->setDescription('Just generates some logs to see whether monolog works')
            ->addArgument('level', InputArgument::REQUIRED, 'Level')
            ->addArgument('repeat', InputArgument::OPTIONAL, 'number of repeats', 1);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $level = $input->getArgument('level');
        $levels = [
            'debug' => Logger::DEBUG,
            'info' => Logger::INFO,
            'warning' => Logger::WARNING,
            'error' => Logger::ERROR,
        ];
        $repeat = $input->getArgument('repeat');
        for ($i = 0; $i < $repeat; $i++) {
            $this->logger->log($levels[$level], 'This is generated log.');
        }

        $output->writeln("Just wrote $repeat log messages.");
        return 0;
    }
}