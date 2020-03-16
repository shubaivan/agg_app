<?php

namespace App\Command;

use App\Kernel;
use League\Csv\Statement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use function League\Csv\delimiter_detect;

class AdtractionResourceHandleFile extends Command
{
    protected static $defaultName = 'app:adtraction:handle';

    private $file = '/download_files/test/sh.csv';

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
        $this->parseCSVContent();
        return 0;
    }

    /**
     * @throws \League\Csv\Exception
     */
    public function parseCSVContent()
    {
        /** @var Reader $csv */
        $csv = Reader::createFromPath($this->kernel->getProjectDir() . $this->file, 'r');
//        $csv->isActiveStreamFilter(); //return true

//        $csv->setDelimiter(',');
        $csv->setHeaderOffset(0);
//        $csv->setEscape('\'');
        $csv->setEnclosure('\'');

        $result = delimiter_detect($csv, [','], 10);
        $count = $result[','];
        $offset = 0;
        while ($offset < $count) {

            //build a statement
            $stmt = (new Statement())
                ->offset($offset)
                ->limit(2);

            //query your records from the document
            $records = $stmt->process($csv);
            $header = $csv->getHeader();
            foreach ($records as $record) {
                $t = $record;
            }

            $offset += 2;
        }
    }
}