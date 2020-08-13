<?php


namespace App\DataFixtures;


use App\Entity\AdminShopsRules;
use App\Entity\Shop;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AdminShopsRulesFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
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
                    'JR', 'Boy', 'Girl', 'Toddler', 'kids', 'juniorer'
                ],
                'description' => [
                    'JR', 'Boy', 'Girl', 'Toddler', 'kids', 'juniorer'
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
        'COS' =>
            [
                'extras' => [
                    'AGE_GROUP' => ['kids']
                ]
            ]
        ,
        'Björn Borg' => [
            'category' => [
                'Barn'
            ],
            '!name' => [
                'Woman', 'Men', 'Dam', 'Herr'
            ],
            '!description' => [
                'Woman', 'Men', 'Dam', 'Herr'
            ]
        ],
        'Twar' =>
            [
                'description' => [
                    'Barn', 'Baby'
                ]
            ],
        'Vegaoo' =>
            [
                'category' => [
                    'Children\'s Clothing'
                ],
                '!name' => [
                    'sex', 'vux'
                ],
                '!description' => [
                    'sex', 'vux'
                ]
            ],
        'Nike' =>
            [
                'category' => [
                    'Kids', 'Barn', 'Girls', 'Boys'
                ]
            ],
        'SneakersPoint' =>
            [
                'category' => [
                    'Sneakers junior'
                ]
            ],
        'Gus Textil' =>
            [
                'name' => [
                    'barn', 'Baby'
                ]
            ],
        'Cykloteket' =>
            [
                'category' => [
                    'Barn, Junior, Baby, Kids'
                ]
            ],
        'Nordic Nest' =>
            [
                'name' => [
                    'barn, baby, Junior'
                ],
                '!name' => [
                    'vas'
                ]
            ]
    ];

    public function load(ObjectManager $manager)
    {
        foreach ($this->configuration as $shopName => $conf) {
            $mapShopNameByKey = Shop::getMapShopNameByKey($shopName);
            if ($mapShopNameByKey) {
                $adminShopsRules = new AdminShopsRules();
                $adminShopsRules
                    ->setStore($shopName)
                    ->setColumnsKeywords($conf);

                $manager->persist($adminShopsRules);
            }
        }

        $manager->flush();

    }

    public function getDependencies()
    {
        return array(
            AdminConfigurationFixtures::class,
        );
    }

    public static function getGroups(): array
    {
        return ['my_pg_fixtures'];
    }
}