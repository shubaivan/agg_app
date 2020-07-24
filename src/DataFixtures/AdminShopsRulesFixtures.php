<?php


namespace App\DataFixtures;


use App\Entity\AdminShopsRules;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AdminShopsRulesFixtures extends Fixture implements DependentFixtureInterface
{
    private $configuration = [
        'Lindex' =>
            [
                'category' => 'Kids wear'
            ]
        ,
        'Åhlens' =>
            [
                'category' => 'Barn & ungdom'
            ]
        ,
        'Sportshopen' =>
            [
                'name' => [
                    'JR', 'Boy', 'Girl', 'Toddler'
                ],
                'description' => [
                    'JR', 'Boy', 'Girl', 'Toddler'
                ],
            ]
        ,
        'FrankDandy' =>
            [
                'category' => [
                    'Boy', 'Girl'
                ]
            ]
        ,
        'Twar.se' =>
            [
                'description' => [
                    'Barn', 'Baby'
                ]
            ]
    ];

    public function load(ObjectManager $manager)
    {
        foreach ($this->configuration as $shopName => $conf) {
            $adminShopsRules = new AdminShopsRules();
            $adminShopsRules
                ->setStore($shopName)
                ->setColumnsKeywords($conf);

            $manager->persist($adminShopsRules);
        }

        $manager->flush();

    }

    public function getDependencies()
    {
        return array(
            AdminConfigurationFixtures::class,
        );
    }
}