<?php

namespace App\Command;

use App\Cache\CacheManager;
use App\Kernel;
use App\QueueModel\FileReadyDownloaded;
use App\Services\Admin\ResourceShopManagement;
use App\Services\Storage\DigitalOceanStorage;
use App\Util\RedisHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use League\Csv\Reader;
use League\Csv\Statement;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\TraceableMessageBus;
use GuzzleHttp\Psr7;

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
     * @var DigitalOceanStorage
     */
    private $do;

    /**
     * @var ResourceShopManagement
     */
    private $resourceShopManagement;

    /**
     * ResourceDownloadFile constructor.
     * @param ResourceShopManagement $resourceShopManagement
     * @param KernelInterface $kernel
     * @param MessageBusInterface $bus
     * @param LoggerInterface $logger
     * @param CacheManager $cacheManager
     * @param RedisHelper $redisHelper
     * @param DigitalOceanStorage $do
     * @param string|null $filePath
     * @param array|null $urls
     */
    public function __construct(
        ResourceShopManagement $resourceShopManagement,
        KernelInterface $kernel,
        MessageBusInterface $bus,
        LoggerInterface $logger,
        CacheManager $cacheManager,
        RedisHelper $redisHelper,
        DigitalOceanStorage $do,
        ?string $filePath,
        ?array $urls
    )
    {
        $this->resourceShopManagement = $resourceShopManagement;
        $this->do = $do;
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
        if (!is_dir($this->getKernel()->getProjectDir() . '/download_files')) {
            mkdir($this->getKernel()->getProjectDir() . '/download_files');
        }
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
            $files = glob($this->getDirForFiles($key) . '/*'); // get all file names
            foreach ($files as $file) { // iterate files
                if (is_file($file))
                    unlink($file); // delete file
            }
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
        $this->resourceShopManagement->guzzleStreamWay(
            $key, $url, $this->dirForFiles, $this->redisUniqKey, $this->getOutput()
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
    protected function getBus()
    {
        return $this->bus;
    }

    /**
     * @param null $key
     * @return string
     */
    protected function getDirForFiles($key = null): string
    {
        $str = $this->getRelativeDirForFiles($key);
        return $this->getKernel()->getProjectDir() . $str;
    }

    /**
     * @param null $key
     * @return string
     */
    protected function getRelativeDirForFiles($key = null): string
    {
        return $this->dirForFiles . ($key ?? '');
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

    /**
     * @param string $key
     * @param string $filePatWithIter
     * @throws \Throwable
     */
    protected function dispatchFileReadyDownload(string $key, string $filePatWithIter): void
    {
        $this->getBus()->dispatch(new FileReadyDownloaded(
                $filePatWithIter,
                $key,
                $this->redisUniqKey)
        );
        $this->getOutput()->writeln(
            '<bg=yellow;options=bold>' . date('H:i:s') . ' success sent queue' . '</>'
        );
    }
}