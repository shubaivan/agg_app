<?php

namespace App\Command;

use App\Kernel;
use App\Services\HandleAdtractionData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class AdtractionResourceHandleFile extends Command
{
    protected static $defaultName = 'app:adtraction:handle';

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var HandleAdtractionData
     */
    private $handleAdtractionData;

    /**
     * AdtractionResourceHandleFile constructor.
     * @param KernelInterface $kernel
     * @param HandleAdtractionData $handleAdtractionData
     * {@inheritDoc}
     */
    public function __construct(
        KernelInterface $kernel,
        HandleAdtractionData $handleAdtractionData
    )
    {
        $this->handleAdtractionData = $handleAdtractionData;
        $this->kernel = $kernel;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:adtraction:handle')
            ->setDescription('Handle adtraction resource file by absolute file path on the server')
            ->addArgument('filePath', InputArgument::REQUIRED, 'Absolute file path on the server');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \League\Csv\Exception
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument('filePath');
        $this->parseCSVContent($filePath);
        return 0;
    }

    /**
     * @param $filePath
     * @throws \League\Csv\Exception
     * @throws \Throwable
     */
    public function parseCSVContent($filePath)
    {
        $this->getHandleAdtractionData()->parseCSVContent($filePath);
    }

    /**
     * @return HandleAdtractionData
     */
    public function getHandleAdtractionData(): HandleAdtractionData
    {
        return $this->handleAdtractionData;
    }
}