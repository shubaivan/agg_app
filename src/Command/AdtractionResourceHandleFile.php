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
            ->addArgument('filePath', InputArgument::REQUIRED, 'Absolute file path on the server')
            ->addArgument('shop', InputArgument::REQUIRED, 'Identity shop for file');
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
        $shop = $input->getArgument('shop');
        $this->parseCSVContent($filePath, $shop);
        return 0;
    }

    /**
     * @param string $filePath
     * @param string $shop
     * @throws \League\Csv\Exception
     * @throws \Throwable
     */
    public function parseCSVContent(string $filePath, string $shop)
    {
        $this->getHandleAdtractionData()->parseCSVContent($filePath, $shop);
    }

    /**
     * @return HandleAdtractionData
     */
    public function getHandleAdtractionData(): HandleAdtractionData
    {
        return $this->handleAdtractionData;
    }
}