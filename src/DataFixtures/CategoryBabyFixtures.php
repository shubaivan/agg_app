<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryBabyFixtures extends AbstractFixtures implements DependentFixtureInterface
{
    /**
     * @var array
     */
    private $configurations = [
        'KATEGORI' => [
            [
                'name' => 'Nappar & Napphållare',
                'key_word' => '
                    Napp, Nappar, tröstnapp, sugnapp, nippel, napper, napphållare,
                    Pacifiers, dummy, pacifier, comforter, nipple                     
                ',
                'sub_key_word' => [
                    'Nappar' => 'Nappar, tröstnapp, sugnapp, nippel, napper,Pacifiers, dummy, pacifier, comforter, nipple',
                    'Napphållare' => 'napphållare'
                ],
            ],
            [
                'name' => 'Nappflaskor',
                'key_word' => '
                    dinappar, dinapp, flaskborste, flaskborstar, sterilisator, vällingmaskin, nappflaskor, nappflaska                     
                ',
                'sub_key_word' => [
                    'Dinappar' => 'dinapp, dinappar',
                    'Flaskborstar' => 'flaskborste, flaskborstar',
                    'Sterilisatorer ' => 'flaskborste, flaskborstar',
                    'Vällingmaskin ' => 'vällingmaskin, vällingmaskiner ',
                    'Nappflaskor' => 'nappfalska, nappflaskor'
                ]
            ],
            [
                'name' => 'Bitleksaker',
                'key_word' => '
                    Bitleksak, bitleksaker, Bitkudde, bitkuddar                     
                ',
            ],
            [
                'name' => 'Äta & Dricka',
                'key_word' => '
                    Bestick, gaffel, gafflar, sked, skedar, kniv, knivar, Haklapp, haklappar, Matservis, Matserviser, Mug, Muggar, Tallrik, Tallrikar, Skål, Skålar, Underlägg,
                    strawcup, strawbottle                     
                ',
                'sub_key_word' => [
                    'Bestick' => 'gaffel, gafflar, kniv, knivar, sked, skedar',
                    'Haklappar' => 'haklapp',
                    'Matserviser' => 'matservis, matserviser',
                    'Muggar & Flaskor' => 'mug, muggar, flaska, flaskor, dryckesbehållare, strawcup, strawbottle',
                    'Tallrikar & Skålar' => 'tallrik, tallrikar, skål, skålar',
                    'Underlägg' => 'underlägg'
                ]
            ],
            [
                'name' => 'Badrum & Hygien',
                'key_word' => '
                    badtermometer, badtermometrar, badbalja, badbaljor, badkarsmatta, badkarsmattor, blöjhink, febertermometer, tandborste, 
                    tandborstar, nagelsax, nagelsaxar, nagelklippare, pallar, pall, tvättlappar, nässug, toasits, Wipes, potta, pottor, badstol, 
                    badstolar, flexibath, hårborste                     
                ',
                'sub_key_word' => [
                    'Badtermometrar' => 'badtermometer',
                    'Badbaljor' => 'badbalja, flexibath, badbaljor',
                    'Badkarsmattor' => 'badkarsmatta, badkarsmattor',
                    'Blöjhinkar' => 'blöjhink',
                    'Febertermometrar' => 'febertermometer',
                    'Tandborstar' => 'tandborste, tandborstar',
                    'Nagelsaxar' => 'nagelsax, nagelklippare, nagelvård',
                    'Pallar' => 'pall',
                    'Tvättlappar' => 'tvättlappar, tvättlapp',
                    'Nässug' => 'nässug',
                    'Toasitsar' => 'toasits, toalettsits',
                    'Wipes' => 'wipes',
                    'Pottor' => 'potta',
                    'Badstolar' => 'badstol',
                    'Hårborste' => 'hårborste, hårborstar'
                ],
            ],
            [
                'name' => 'Baby Skötväskor',
                'key_word' => '
                    Skötväskor, skötväska, Transportväska                     
                ',
            ],
            [
                'name' => 'Snuttefiltar',
                'key_word' => '
                    snuttefilt, snuttefiltar                     
                ',
            ],
            [
                'name' => 'Mobiler & Speldosor',
                'key_word' => '
                    mobil, mobiler, mobilarm, mobilarmar, speldosor, speldosa                     
                ',
                'sub_key_word' => [
                    'Mobiler' => 'mobil, mobiler, mobilarm, mobilarmar',
                    'Speldosor' => 'speldosa, speldosor'
                ]
            ],
            [
                'name' => 'Hoppgungor',
                'key_word' => '
                    Hoppgungor, Hoppgunga                     
                ',
            ],
            [
                'name' => 'Babygym & Lekmattor',
                'key_word' => '
                    Babygym, lekmatta, lekmattor, aktivitetsmatta, activity play mat, Lekmatta                      
                ',
            ],
            [
                'name' => 'Skötbord & skötbyråer',
                'key_word' => '
                    skötbord, skötbyrå                      
                ',
                'sub_key_word' => [
                    'Skötbord' => 'skötbord',
                    'Skötbyråer' => 'skötbyrå'
                ]
            ],
            [
                'name' => 'Matstolar',
                'key_word' => '
                    Matstol, Matstolar                      
                ',
            ],
            [
                'name' => 'Gåstolar',
                'key_word' => '
                    Läragåstol, Gåstol                      
                ',
            ],
            [
                'name' => 'Babysitter',
                'key_word' => '
                    Babysitter                      
                ',
            ],
            [
                'name' => 'Övrigt',
                'key_word' => '
                    pulverbehållare, plåster, hörnskydd, Hörselskydd, Resväska, Reflexväst, Cykelstol, Cykelsits, Baby Watch, Spisskydd, Resesäng                   
                ',
            ],

        ],
    ];

    public function load(ObjectManager $manager)
    {
        $this->setManager($manager);

        $main = $this->createCategoryWithConf(
            'Baby',
            'Baby, Toddler, Infant, Premature, eStore, kids',
            'main',
            'Barn, Skor, Barnvagnar, Leksaker, Förälder, Maskerad, Kostym, Barndräkt, Halloween, Utklädnad, Sagodräkt, maskeradkläder');
        $configurations = $this->configurations;
        $this->processConfiguration($configurations, $main);

        $this->afterLoad();
    }

    public function getDependencies()
    {
        return array(
            CategorySkorFixtures::class,
        );
    }
}
