<?php

namespace App\Command;

use App\Kernel;
use App\QueueModel\FileReadyDownloaded;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\TraceableMessageBus;

class AdtractionResourceDownloadFile extends Command
{
    protected static $defaultName = 'app:adtraction:download';

    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $dirForFiles;

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var TraceableMessageBus
     */
    private $bus;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * AdtractionResourceDownloadFile constructor.
     * @param KernelInterface $kernel
     * @param MessageBusInterface $bus
     * @param LoggerInterface $adtractionLogLogger
     * @param ContainerBagInterface $params
     */
    public function __construct(
        KernelInterface $kernel,
        MessageBusInterface $bus,
        LoggerInterface $adtractionLogLogger,
        ContainerBagInterface $params
    )
    {
        $this->url = $params->get('adtraction_download_url');
        $this->dirForFiles = $params->get('adtraction_download_file_path');
        $this->kernel = $kernel;
        $this->bus = $bus;
        $this->logger = $adtractionLogLogger;
        parent::__construct();
        $this->postConstruct();
    }

    /**
     * @return void
     */
    private function postConstruct()
    {
        if (!is_dir($this->getDirForFiles())) {
            // dir doesn't exist, make it
            mkdir($this->getDirForFiles());
        }
    }

    protected function configure()
    {
        $this
            ->setDescription('Download file from adtraction resource')
            ->setHelp('
                This command download file from adtraction resource and save it in ' . $this->getDirForFiles() . ' with timestamp name
            ');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info('test');
        $this->setOutput($output);
        $output->writeln([
            'Adtraction resource download file',
            '============',
            '<fg=green;options=bold,underscore>Start</>',
        ]);

        try {
            $this->guzzleStreamWay();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->getApplication()->renderThrowable($e, $output);
        } catch (\Throwable $t) {
            $this->logger->error($t->getMessage());
            $this->getApplication()->renderThrowable($t, $output);
        }

        return 0;
    }

    /**
     * @throws \Throwable
     */
    public function guzzleStreamWay()
    {
        $client = new Client();
        $response = $client->request(
            'GET',
            $this->url,
            ['stream' => true]
        );
        $body = $response->getBody();
        $this->getOutput()->writeln(
            '<fg=green>' . date('H:i:s') . ' guzzle stream way get body' . '</>'
        );
        $fileRelativePath = $this->getDirForFiles() . date('YmdHis') . '.csv';
        // Read bytes off of the stream until the end of the stream is reached
        while (!$body->eof()) {
            file_put_contents(
                $fileRelativePath,
                $body->read(1024),
                FILE_APPEND
            );
        }
        $this->getOutput()->writeln(
            '<fg=green>' . date('H:i:s') . ' finish download file: ' . $fileRelativePath . '</>'
        );
        $this->getBus()->dispatch(new FileReadyDownloaded($fileRelativePath));
        $this->getOutput()->writeln(
            '<bg=yellow;options=bold>' . date('H:i:s') . ' success sent queue' . '</>'
        );
    }

    /**
     * @return Kernel
     */
    protected function getKernel(): Kernel
    {
        return $this->kernel;
    }

    /**
     * @return TraceableMessageBus
     */
    protected function getBus(): TraceableMessageBus
    {
        return $this->bus;
    }

    /**
     * @return string
     */
    protected function getDirForFiles(): string
    {
        return $this->getKernel()->getProjectDir() . $this->dirForFiles;
    }

    /**
     * @return OutputInterface
     */
    protected function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     */
    protected function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }
}