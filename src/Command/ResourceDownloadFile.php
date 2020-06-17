<?php

namespace App\Command;

use App\Cache\CacheManager;
use App\Kernel;
use App\QueueModel\FileReadyDownloaded;
use App\Util\RedisHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\TraceableMessageBus;

class ResourceDownloadFile extends Command
{
    protected static $defaultName = 'app:resource:download';

    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
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
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var RedisHelper
     */
    private $redisHelper;

    /**
     * @var string
     */
    protected $redisUniqKey;

    /**
     * ResourceDownloadFile constructor.
     * @param KernelInterface $kernel
     * @param MessageBusInterface $bus
     * @param LoggerInterface $logger
     * @param CacheManager $cacheManager
     * @param RedisHelper $redisHelper
     * @param string $filePath
     * @param array $urls
     */
    public function __construct(
        KernelInterface $kernel,
        MessageBusInterface $bus,
        LoggerInterface $logger,
        CacheManager $cacheManager,
        RedisHelper $redisHelper,
        ?string $filePath,
        ?array $urls
    )
    {
        $this->cacheManager = $cacheManager;
        $this->url = $urls;
        $this->dirForFiles = $filePath;
        $this->kernel = $kernel;
        $this->bus = $bus;
        $this->logger = $logger;
        $this->redisHelper = $redisHelper;

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

        foreach ($this->getUrl() as $key => $value) {
            if (!is_dir($this->getDirForFiles($key))) {
                // dir doesn't exist, make it
                mkdir($this->getDirForFiles($key));
            }
        }
    }

    protected function configure()
    {
        $this
            ->setDescription('Download file from resource')
            ->setHelp('
                This command download file from resource and save it in ' . $this->getDirForFiles() . ' with timestamp name
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
        $this->setOutput($output);
        $output->writeln([
            'resource download file',
            '============',
            '<fg=green;options=bold,underscore>Start</>',
        ]);

        try {
            $this->getCacheManager()->clearAllPoolsCache();
            $this->createGuzzleStreamWayForEachUrl();
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
    protected function createGuzzleStreamWayForEachUrl()
    {
        if (!$this->redisUniqKey) {
            $this->redisHelper
                ->hIncrBy('attempt', date('Ymd'));
            $this->redisUniqKey = date('Ymd') . '_' . $this->redisHelper->hGet('attempt', date('Ymd'));
        }

        foreach ($this->getUrl() as $key => $url) {
            $this->guzzleStreamWay($key, $url);
        }
    }

    /**
     * @param string $key
     * @param string $url
     * @throws \Throwable
     */
    protected function guzzleStreamWay(string $key, string $url)
    {
        $client = new Client();
        try {
            $response = $client->request(
                'GET',
                $url,
                [
                    'stream' => true,
                    'version' => '1.0'
                ]
            )->getBody();
        } catch (ClientException $exception) {
            if ($exception->getCode() === 403 || $exception->getCode() === 404) {
                $this->getOutput()->writeln(
                    '<fg=red>' . date('H:i:s') . 'shop: ' . $key .' error code: ' . $exception->getCode() . 'message: ' .$exception->getMessage() . '</>'
                );
                return;
            } else {
                throw $exception;
            }
        }

        $phpStream = $response->detach();
        unset($client);
        unset($response);

        $this->getOutput()->writeln(
            '<fg=green>' . date('H:i:s') . ' guzzle stream way get body' . '</>'
        );
        $date = date('YmdHis');
        $fileRelativePath = $this->getDirForFiles($key) . '/' . $date . '.csv';
        // Read bytes off of the stream until the end of the stream is reached
        while (!feof($phpStream)) {
            $read = fread($phpStream, 1024);

            $read = str_replace('""\'', '\"\"\'', $read);
            $read = str_replace('"\'', '\"\\\'', $read);

            file_put_contents(
                $fileRelativePath,
                $read,
                FILE_APPEND
            );
        }
        $this->getOutput()->writeln(
            '<fg=green>' . date('H:i:s') . ' finish download file: ' . $fileRelativePath . '</>'
        );
        $this->getBus()->dispatch(new FileReadyDownloaded($fileRelativePath, $key, $this->redisUniqKey));
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
     * @param null $key
     * @return string
     */
    protected function getDirForFiles($key = null): string
    {
        return $this->getKernel()->getProjectDir() . $this->dirForFiles . ($key ?? '');
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

    /**
     * @return array
     */
    protected function getUrl(): array
    {
        return $this->url;
    }

    /**
     * @return CacheManager
     */
    protected function getCacheManager(): CacheManager
    {
        return $this->cacheManager;
    }
}