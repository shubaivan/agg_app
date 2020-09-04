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
                    Sneaker, Sneakers, Basketball Sneakers, Basketball Sneaker, Slip-on sneaker, 
                    Gympaskor, Gympasko, revolution, basketsko, basketskor                      
                ',
                'sub_key_word' => [
                    'Sneaker' => 'sneakers, sneaker, revolution, Slip-on sneaker, gympaskor, gympasko',
                    'Basketskor' => 'basketskor, basketballshoes, basketballsneaker, basketball sneaker'
                ]
            ],
            [
                'name' => 'Tofflor & Sandaler',
                'key_word' => '
                    Sandal, Sandaler, Flipflop, Flipflops, Flip-flop, Flip-flops, Tofflor, Toffla                     
                ',
                'sub_key_word' => [
                    'Sandaler' => 'Sandaler, sandals',
                    'Flipflops' => 'Flipflops, flip flop, flip-flops',
                    'Tofflor' => 'Tofflor, slippers'
                ],
                'negative_key_words' => 'gummistövlar , gummistövel, sneakers, sneaker, kängor, boots, gummistövel'
            ],
            [
                'name' => 'Gummistövlar',
                'key_word' => '
                    Gummistövlar, Rubber boots, Rain boots, Rain boot, Rubber boot                     
                ',
                'sub_key_word' => [
                    'sub Gummistövlar' => 'Gummistövlar',
                ]
            ],
            [
                'name' => 'Boots, stövlar & kängor',
                'key_word' => '
                    Vinter Kängor, Vinterkängor, Vinterkänga, Vinter Känga, Vinterboots, Vinterboot, 
                    Vinter Boots, Vinter Boot, Vinterskor, Vinter Skor, Vinter Sko, Vinter Skor, 
                    Vinterstövlar, Vinterstövel, Vinter Stövlar, Vinter Stövel, Kängor, Känga, Boots,
                    Boot, BTS stövlar                     
                ',
                'sub_key_word' => [
                    'Boots' => 'Boots',
                    'Stövlar' => 'Stövlar',
                    'Kängor' => 'Kängor'
                ],
                'negative_key_words' => 'Robeez, Shoes, Sneakers, Adidas, Dolce & Gabbana, Easy Peasy,
                 Sandaler, Raspberry, Lacoste, Absorba'
            ],
            [
                'name' => 'Finskor',
                'key_word' => '
                    Finskor, Finsko, Festsko, Festskor, Fest sko, Fest skor, Fin sko, Fin skor, Brogue skor, 
                    Brogueskor, Bobby skor, Bobbyskor, Lackade skor, Lackade T-skor, Wouf skor, 
                    School skor, Asther skor, Asher skor, Rufus Edge Skor, Blake Street Skor, Rock Verve Läder Skor,
                    Tiny Dusk Skor, Tiny Mist Skor, Etch Spark Skor, Oxford BTS Brogues Skor, Scape Sky Läder Skor,
                    Etch Strap Lackade Skor, Mendip Root Skor, Street Shine Skor, Drew Star Skor, Groove Skor,
                    Desert Trek, Hula Thrill Skor, Drew Wow Skor, Venture Walk Skor, Crown Blaze Skor,
                    Junior Riddock Velcro Skor, Junior Arzach Velcro School Skor, Läder Jiri School Skor, 
                    Kick Lo Velcro School Skor                     
                ',
                'sub_key_word' => [
                    'sub Finskor' => 'Finskor, fin sko, fin-sko, festsko, fest sko, fest-sko, festskor, fest skor, fest-skor',
                ],
                'negative_key_words' => 'midjan'
            ],
            [
                'name' => 'Espadrillos',
                'key_word' => '
                    Espadrillos, Espadrillo, Espadriller, Espadrill                     
                ',
                'sub_key_word' => [
                    'sub Espadrillos' => 'Espadrillos'
                ]
            ],
            [
                'name' => 'Badskor',
                'key_word' => '
                    Badskor, Badsko                     
                ',
                'sub_key_word' => [
                    'sub Badskor' => 'Badskor, swimmingshoes',
                    'Strandskor' => 'Strandskor, beach shoes',
                ],
                'negative_key_words' => 'badskum, badsköldpadda'
            ],
            [
                'name' => 'Ballerinaskor & Tygskor',
                'key_word' => '
                    Seglarsko, Seglarskor, Ballerinaskor, Ballerina skor, Ballerinasko, 
                    Ballerina sko, tygsko, tygskor, vans skor      
                ',
                'sub_key_word' => [
                    'Tygskor' => 'tygsko, tygskor, vans skor',
                    'Ballerinaskor' => 'Ballerinaskor, Ballerina skor, Ballerinasko, Ballerina sko',
                    'Seglarskor' => 'Seglarskor, sailingshoes'
                ],
                'negative_key_words' => 'Tygskodd'
            ],
            [
                'name' => 'Träningsskor',
                'key_word' => '
                    Träningsskor, Fotbollsskor, Trainers, Tennisskor, Dansskor, golf                   
                ',
                'sub_key_word' => [
                    'sub Träningsskor' => 'Träningsskor, trainers, trainingshoes',
                    'Fotbollsskor' => 'Fotbollsskor, soccershoes, soccer shoes, fotballshoes'
                ]
            ],
            [
                'name' => 'Babyskor',
                'key_word' => '
                    Babysko, Babyskor, Baby sko, Baby skor, Babytossor, Baby tossor, Babytossa, Baby tossa,
                    Lära-gå-skor, Lära gå skor, Lära gå sko, Lära-gå-sko, Sockiplast,
                    Sock sneakers, Babytofflor, Baby tofflor, Cribskor, Crib skor, Crib sandaler, 
                    Cribsandaler, EZ sneaker, Doptofflor, Dop tofflor, Starter skor, Starterskor, Starter sko, 
                    Startersko, tossor, tossa, Infants sneaker, Infant sneaker, Infants sneakers                      
                ',
                'sub_key_word' => [
                    'Baby tofflor' => 'Babytofflor, baby tofflor, babyslipper, baby slipper',
                    'Lära-gå-skor' => 'Lära-gå-skor, lära gå skor, lära-gå-sko, lära gå sko',
                    'Baby skor' => 'Baby skor, Babyskor, baby shoes, babyshoes, baby sko, babysko, baby shoe, babyshoe'
                ],
                'negative_key_words' => 'tossie'
            ],
        ]
    ];

    private $configurationsSize = [
        'SHOPPA EFTER STORLEK (EU)' => [
            '0-1 ÅR (STORLEK 16 - 20)' => [
                'sizes' => '16, 20'
            ],
            '1-3 ÅR (STORLEK 20-26)' => [
                'sizes' => '20, 26'
            ],
            '4-6 ÅR (STORLEK 26-31)' => [
                'sizes' => '26, 31'
            ],
            '7-10 ÅR (STORLEK 31-34)' => [
                'sizes' => ' 31, 34',
            ],
            'ÖVER 10 ÅR (34 & STÖRRE)' => [
                'sizes' => '34, 1000'
            ],
        ]
    ];


    public function load(ObjectManager $manager)
    {
        $this->setManager($manager);

        $main = $this->createCategoryWithConf(
            'Skor',
            'skor, shoes, sandals, sandaler, sko, Toffla, Sneakers, Sneaker, Finskor, Gummistövlar, Boots, Stövlar, kängor, Espadrillos, Badskor',
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
            CategoryKladerFixtures::class,
        );
    }
}
