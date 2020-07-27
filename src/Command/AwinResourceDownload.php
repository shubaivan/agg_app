<?php


namespace App\Command;


use App\Cache\CacheManager;
use App\Util\RedisHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class AwinResourceDownload extends ResourceDownloadFile
{
    protected static $defaultName = 'app:awin:download';

    /**
     * AwinResourceDownload constructor.
     * @param KernelInterface $kernel
     * @param MessageBusInterface $bus
     * @param LoggerInterface $adrecordLogLogger
     * @param ContainerBagInterface $params
     * @param CacheManager $cacheManager
     * @param RedisHelper $redisHelper
     */
    public function __construct(
        KernelInterface $kernel,
        MessageBusInterface $bus,
        LoggerInterface $adrecordLogLogger,
        ContainerBagInterface $params,
        CacheManager $cacheManager,
        RedisHelper $redisHelper
    )
    {

        $url = $params->get('awin_download_urls');
        $dirForFiles = $params->get('awin_download_file_path');
        parent::__construct(
            $kernel,
            $bus,
            $adrecordLogLogger,
            $cacheManager,
            $redisHelper,
            $dirForFiles,
            $url
        );

    }

    protected function configure()
    {
        $this
            ->setDescription('Download file from awin resource')
            ->setHelp('
                This command download file from awin resource and save it in ' . $this->getDirForFiles() . ' with timestamp name
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
        $this->logger->info('awin');
        $this->setOutput($output);
        $output->writeln([
            'awin resource download file',
            '============',
            '<fg=green;options=bold,underscore>Start</>',
        ]);

        try {
            //$this->getCacheManager()->clearAllPoolsCache();
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
}