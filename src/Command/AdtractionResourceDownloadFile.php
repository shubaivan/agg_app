<?php

namespace App\Command;

use App\Kernel;
use GuzzleHttp\Client;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use function League\Csv\delimiter_detect;

class AdtractionResourceDownloadFile extends Command
{
    protected static $defaultName = 'app:adtraction:download';

//    private $url = 'https://adtraction.com/productfeed.htm?type=feed&format=CSV&encoding=UTF8&epi=0&zip=0&cdelim=tab&tdelim=singlequote&sd=0&sn=0&flat=0&apid=1136856383&asid=1039629367';
    private $url = 'https://adtraction.com/productfeed.htm?type=feed&format=CSV&encoding=UTF8&epi=0&zip=0&cdelim=comma&tdelim=singlequote&sd=0&sn=0&flat=0&apid=1136856383&asid=1039629367';
    private $dirForFiles = '/download_files/test/';


    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * AdtractionResource constructor.
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        parent::__construct();
    }


    protected function configure()
    {
        // ...
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \League\Csv\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->guzzleWay();

        return 0;
    }

    public function guzzleWay()
    {
        $client = new Client();
        $response = $client->request(
            'GET',
            $this->url,
            ['stream' => true]
        );
// Read bytes off of the stream until the end of the stream is reached
        $body = $response->getBody();
        $t = '';
        $date = date('YmdHis');
        while (!$body->eof()) {
            $t = $body->read(1024);
            file_put_contents(
                $this->kernel->getProjectDir() . $this->dirForFiles . $date . '.csv',
                $t,
                FILE_APPEND
            );
//            $this->parseCSVContent($t);
        }
        $y = 1;

//        $this->parseCSVContent($t);
    }


}