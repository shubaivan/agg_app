<?php


namespace App\Services\Admin;


use App\Kernel;
use App\QueueModel\FileReadyDownloaded;
use App\Services\Storage\DigitalOceanStorage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\TraceableMessageBus;

class ResourceShopManagement
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var DigitalOceanStorage
     */
    private $do;

    /**
     * @var TraceableMessageBus
     */
    private $bus;

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var string
     */
    private $dirForFiles;

    /**
     * @var string
     */
    protected $redisUniqKey;

    /**
     * ResourceShopManagement constructor.
     * @param DigitalOceanStorage $do
     * @param MessageBusInterface $bus
     */
    public function __construct(
        KernelInterface $kernel,
        DigitalOceanStorage $do,
        MessageBusInterface $bus)
    {
        $this->kernel = $kernel;
        $this->do = $do;
        $this->bus = $bus;
    }

    /**
     * @param string $key
     * @param string $url
     * @param string $dirForFiles
     * @param string $redisUniqKey
     * @param OutputInterface|null $output
     * @throws \League\Flysystem\FileExistsException
     * @throws \Throwable
     */
    public function guzzleStreamWay(
        string $key,
        string $url,
        string $dirForFiles,
        string $redisUniqKey,
        ?OutputInterface $output = null
    )
    {
        $this->redisUniqKey = $redisUniqKey;
        $this->dirForFiles = $dirForFiles;
        if ($output) {
            $this->setOutput($output);
        }

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
            if ($exception->getCode() === 403
                || $exception->getCode() === 404
                || $exception->getCode() === 400
            ) {
                $this->writelnOutPut(
                    '<fg=red>' . date('H:i:s') . 'shop: ' . $key . ' error code: ' . $exception->getCode()
                    . 'message: ' . $exception->getMessage() . '</>'
                );
                return;
            } else {
                throw $exception;
            }
        }
        $date = date('YmdHis');

        // Read bytes off of the stream until the end of the stream is reached
        $doPath = $this->getRelativeDirForFiles($key) . '/' . $date . '.csv';

        $metadata = $response->getMetadata();

        $phpStream = $response->detach();
        unset($client);
        unset($response);

        $this->writelnOutPut('<fg=green>' . date('H:i:s') . ' guzzle phpStream detach, unset' . '</>');

        if (isset($metadata['wrapper_data']) && is_array($metadata['wrapper_data'])) {
            foreach ($metadata['wrapper_data'] as $meta) {
                if ($meta == 'Content-Type: application/zip') {
                    $this->writelnOutPut('<fg=green>' . date('H:i:s') . ' awin zop' . '</>');

                    $fileRelativePath = $this->getDirForFiles($key) . '/' . $date . '.csv.zip';

                    while (!feof($phpStream)) {
                        $read = fread($phpStream, 2048);

                        file_put_contents(
                            $fileRelativePath,
                            $read,
                            FILE_APPEND
                        );
                    }
                    $this->writelnOutPut(
                        '<fg=green>' . date('H:i:s') . ' finish download file: ' . $fileRelativePath . '</>'
                    );

                    $zip = new \ZipArchive();
                    if ($zip->open($fileRelativePath) === TRUE) {
                        for ($i = 0; $i < $zip->numFiles; $i++) {
                            $filename = $zip->getNameIndex($i);
                            $filePatWithIter = $this->getDirForFiles($key) . '/' . $date . '.csv';
                            copy("zip://" . $fileRelativePath . "#" . $filename, $filePatWithIter);
                        }
                        $zip->close();
                        unlink($fileRelativePath);
                        $this->writelnOutPut(
                            '<fg=green>' . date('H:i:s') . ' finish unzip file: ' . $filePatWithIter . '</>'
                        );
                    }

                    $phpStream = fopen($filePatWithIter, 'r');
                }
            }
        }

        $this->do->getStorage()->writeStream($doPath, $phpStream);

        $this->writelnOutPut('<fg=green>' . date('H:i:s') . ' finish writeStream doPath: ' . $doPath . '</>');

        $this->dispatchFileReadyDownload($key, $doPath);

        $this->writelnOutPut('<bg=yellow;options=bold>' . date('H:i:s') . ' success' . '</>');
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
        $this->writelnOutPut('<bg=yellow;options=bold>' . date('H:i:s') . ' success sent queue' . '</>');
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
        $str = $this->getRelativeDirForFiles($key);
        return $this->getKernel()->getProjectDir() . $str;
    }

    /**
     * @return Kernel
     */
    protected function getKernel(): Kernel
    {
        return $this->kernel;
    }

    private function writelnOutPut(string $message)
    {
        if ($this->output) {
            $this->output->writeln($message);
        }
    }
}