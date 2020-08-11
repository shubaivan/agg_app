<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategorySkorFixtures extends AbstractFixtures implements DependentFixtureInterface
{
    /**
     * @var array
     */
    private $configurations = [
        'KATEGORI' => [
            [
                'name' => 'Sneakers',
                'key_word' => '
                    Sneaker, Sneakers, Basketball Sneakers, Basketball Sneaker, Slipp-on sneaker, Gympaskor, Gympasko                     
                ',
                'sub_key_word' => [
                    'subSneakers' => 'Sneakers',
                    'Basketskor' => 'Basketskor'
                ]
            ],
            [
                'name' => 'Tofflor & Sandaler',
                'key_word' => '
                    Sandal, Sandaler, Flipflop, Flipflops, Flip-flop, Flip-flops, Tofflor, Toffla                     
                ',
                'sub_key_word' => [
                    'Sandaler' => 'Sandaler',
                    'Flipflops' => 'Flipflops',
                    'Tofflor' => 'Tofflor',
                ]
            ],
            [
                'name' => 'Gummistövlar',
                'key_word' => '
                    Gummistövlar, 
                    Rubber boots, Rain boots, Rain boot, Rubber boot                     
                ',
                'sub_key_word' => [
                    'subGummistövlar' => 'Gummistövlar',
                ]
            ],
            [
                'name' => 'Boots, stövlar & kängor',
                'key_word' => '
                    Vinter Kängor, Vinterkängor, Vinterkänga, Vinter Känga, Vinterboots, Vinterboot, Vinter Boots, Vinter Boot, Vinterskor, Vinter Skor, Vinter Sko, Vinter Skor, 
                    Vinterstövlar, Vinterstövel, Vinter Stövlar, Vinter Stövel, Kängor, Känga, Boots, Boot, BTS stövlar                     
                ',
                'sub_key_word' => [
                    'Stövlar' => 'Stövlar',
                    'Kängor' => 'Kängor',
                ],
                'negative_key_words' => 'Robeez, Shoes, Sneakers, Adidas, Dolce, Gabbana, Easy Peasy, Sandaler, Raspberry, Lacoste, Absorba'
            ],
            [
                'name' => 'Finskor',
                'key_word' => '
                    Finskor, Finsko, Festsko, Festskor, Fest sko, Fest skor, Fin sko, Fin skor, Brogue skor, Brogueskor, Bobby skor, Bobbyskor, Lackade skor, Lackade T-skor, Wouf skor, 
                    School skor, Asther skor, Asher skor, Rufus Edge Skor, Blake Street Skor, Rock Verve Läder Skor, Tiny Dusk Skor, Tiny Mist Skor, Etch Spark Skor, 
                    Oxford BTS Brogues Skor, Scape Sky Läder Skor, Etch Strap Lackade Skor, Mendip Root Skor, Street Shine Skor, Drew Star Skor, Groove Skor, Desert Trek, 
                    Hula Thrill Skor, Drew Wow Skor, Venture Walk Skor, Crown Blaze Skor, Junior Riddock Velcro Skor, Junior Arzach Velcro School Skor, Läder Jiri School Skor, 
                    Kick Lo Velcro School Skor                     
                ',
                'sub_key_word' => [
                    'subFinskor' => 'Finskor, fin sko, fin-sko, festsko, fest sko, fest-sko, festskor, fest skor, fest-skor',
                ],
                'negative_key_words' => 'midjan'
            ],
            [
                'name' => 'Espadrillos',
                'key_word' => '
                Espadrillos, Espadrillo, Espadriller, Espadrill                     
            ',
                'sub_key_word' => [
                    'subEspadrillos' => 'Espadrillos'
                ]
            ],
            [
                'name' => 'Badskor',
                'key_word' => '
                Badskor, Badsko                     
            ',
                'sub_key_word' => [
                    'subBadskor' => 'Badskor, swimmingshoes',
                    'Strandskor' => 'Strandskor, beach shoes',
                ]
            ],
            [
                'name' => 'Tygskor',
                'key_word' => '
                Seglarsko, Seglarskor                     
            ',
                'sub_key_word' => [
                    'Seglarskor' => 'Seglarskor, sailingshoes',
                ]
            ],
            [
                'name' => 'Träningsskor',
                'key_word' => '
                Träningsskor, Fotbollsskor, Trainers, Tennisskor, Dansskor, Ballerinaskor, Ballerina skor, Ballerinasko, Ballerina sko                     
            ',
                'sub_key_word' => [
                    'subTräningsskor' => 'Träningsskor, trainers, trainingshoes',
                    'Fotbollsskor' => 'Fotbollsskor, soccershoes, soccer shoes, fotballshoes',
                    'Ballerinaskor' => 'Ballerinaskor, ballerinas, ballerinashoes',
                ],
                'negative_key_words' => 'ballerinasko'
            ],
            [
                'name' => 'Babyskor',
                'key_word' => '
                Babysko, Babyskor, Baby sko, Baby skor, Babytossor, Baby tossor, Babytossa, Baby tossa, Lära-gå-skor, Lära gå skor, Lära gå sko, Lära-gå-sko, Sockiplast,
                Sock sneakers, Babytofflor, Baby tofflor, Cribskor, Crib skor, Crib sandaler, Cribsandaler, EZ sneaker, Doptofflor, Dop tofflor, Starter skor, Starterskor, Starter sko, 
                Startersko,
                Infants sneaker, Infant sneaker, Infants sneakers                     
            ',
                'sub_key_word' => [
                    'Baby tofflor' => 'Babytofflor, baby tofflor, babyslipper, baby slipper',
                    'Lära-gå-skor' => 'Lära-gå-skor, lära gå skor, lära-gå-sko, lära gå sko',
                    'Baby skor' => 'Baby skor, Babyskor, baby shoes, babyshoes, baby sko, babysko, baby shoe, babyshoe',
                ]
            ],
        ]
    ];

    private $configurationsSize = [
        'SHOPPA EFTER STORLEK (EU)' => [
            '15 - 20' => [
                'sizes' => '15, 16, 17, 18, 19, 20',
                'positive_key_words' => 'boy, girl',
            ],
            '21 - 25' => [
                'sizes' => '21, 22, 23, 24, 25',
                'positive_key_words' => 'boy, girl',
            ],
            '26 - 30' => [
                'sizes' => '26, 27, 28, 29, 30',
                'positive_key_words' => 'boy, girl',
            ],
            '31 - 35' => [
                'sizes' => ' 31, 32, 33, 34, 35',
                'positive_key_words' => 'boy, girl',
            ],
            '36 - 40' => [
                'sizes' => '36, 37, 38, 39, 40',
                'positive_key_words' => 'boy, girl',
            ],
            'Flicka' => [
                'sizes' => '15, 40',
                'positive_key_words' => 'girl',
            ],
            'Pojke' => [
                'sizes' => '15, 40',
                'positive_key_words' => 'boy',
            ],
        ]
    ];


    public function load(ObjectManager $manager)
    {
        $this->setManager($manager);

        $main = $this->createCategoryWithConf(
            'Skor',
            'skor, shoes, sandals, sandaler, sko, Toffla',
            'main'
        );
        $configurations = $this->configurations;
        $this->processConfiguration($configurations, $main);
        $this->processSizeCategories($this->configurationsSize, $main);
        $this->afterLoad();
    }

    public function getDependencies()
    {
        return array(
            CategoryBarnFixtures::class,
        );
    }
}
