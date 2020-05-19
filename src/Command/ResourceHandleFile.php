<?php

namespace App\Command;

use App\Kernel;
use App\Services\HandleDownloadFileData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ResourceHandleFile extends Command
{
    protected static $defaultName = 'app:adtraction:handle';

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var HandleDownloadFileData
     */
    private $handleDownloadData;

    /**
     * ResourceHandleFile constructor.
     * @param KernelInterface $kernel
     * @param HandleDownloadFileData $handleAdtractionData
     * {@inheritDoc}
     */
    public function __construct(
        KernelInterface $kernel,
        HandleDownloadFileData $handleAdtractionData
    )
    {
        $this->handleDownloadData = $handleAdtractionData;
        $this->kernel = $kernel;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:resource:handle')
            ->setDescription('Handle some resource file by absolute file path on the server')
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
        $this->getHandleDownloadData()->parseCSVContent($filePath, $shop);
    }

    /**
     * @return HandleDownloadFileData
     */
    public function getHandleDownloadData(): HandleDownloadFileData
    {
        return $this->handleDownloadData;
    }
}