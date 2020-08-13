<?php


namespace App\DataFixtures\Mysql;


use App\EntityMysql\Colours;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use League\Csv\Reader;
use Symfony\Component\HttpKernel\KernelInterface;

class ColoursFixtures extends Fixture implements FixtureGroupInterface
{

    /**
     * @var ManagerRegistry $registry
     */
    private $registry;

    /**
     * @var KernelInterface
     */
    private $appKernel;

    /**
     * ColoursFixtures constructor.
     * @param ManagerRegistry $registry
     * @param KernelInterface $appKernel
     */
    public function __construct(
        ManagerRegistry $registry,
        KernelInterface $appKernel
    )
    {
        $this->registry = $registry;
        $this->appKernel = $appKernel;
    }


    /**
     * Load data fixtures with the passed EntityManager
     */
    public function load(ObjectManager $manager)
    {
        $objectManager = $this->registry->getManager('manager_mysql');

        $projectDir = $this->appKernel->getProjectDir();
        $csv = Reader::createFromPath($projectDir.'/mysql/colours.csv', 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(',');
        $colours = [];
        foreach ($csv as $record) {
            $colours[mb_strtolower(trim($record['original']))] = ucfirst(mb_strtolower(trim($record[' substitute'])));
        }

        foreach ($colours as $originalColor=>$substituteColor) {
            $colours = new Colours();
            $colours
                ->setOriginalColor($originalColor)
                ->setSubstituteColor($substituteColor);

            $objectManager->persist($colours);
        }

        $objectManager->flush();
    }

    /**
     * This method must return an array of groups
     * on which the implementing class belongs to
     *
     * @return string[]
     */
    public static function getGroups(): array
    {
        return ['my_mysql'];
    }
}