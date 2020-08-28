<?php


namespace App\DataFixtures;


use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryInredningAndSakerhetFixtures extends AbstractFixtures implements DependentFixtureInterface
{
    /**
     * @var array
     */
    private $configurations = [
        'KATEGORI INREDNING' => [
            [
                'name' => 'Barnmöbler',
                'key_word' => '
                    Barnmöbler                
                ',
            ],
            [
                'name' => 'Barnrumsförvaring',
                'key_word' => '
                    Barnrumsförvaring
                ',
            ],
            [
                'name' => 'Barnrumsmattor',
                'key_word' => '
                    Barnrumsmattor
                ',
            ],
            [
                'name' => 'Barnrumspaket',
                'key_word' => '
                    Barnrumspaket
                ',
            ],
            [
                'name' => 'Barnsängar',
                'key_word' => '
                    Barnsängar
                ',
            ],
            [
                'name' => 'Sängtextilier',
                'key_word' => '
                    Sängtextilier
                ',
            ],
            [
                'name' => 'Täcken',
                'key_word' => '
                    lock, skydd, fodral, omslag, täckning, täcke, vaddtäcke, sängtäcke, Täcken,
                    blanket, felt, wrap, plaid, rug
                ',
            ],
            [
                'name' => 'Kuddar',
                'key_word' => '
                    kudde, huvudkudde, dyna, Kuddfodral, Kuddar,
                    pillow, cushion, pad, pillar, sachet
                ',
            ],
            [
                'name' => 'Madrasser',
                'key_word' => '
                    madrass, mattress, Madrasser,
                    mattresses, bedtick
                ',
            ],
            [
                'name' => 'Lekhagar',
                'key_word' => '
                    barnhage, lekhage, daghem, barnkammare, Lekhagar,
                    playpens, playpen, nurseries, nursery
                ',
            ],
            [
                'name' => 'Belysning',
                'key_word' => '
                    lyse, upplysning, kristallkrona, lampa, ljus, ljusöppning, trafikljus, framstående person, fyr, Belysning,
                    Lighting,  illumination, light, illustration, lamp 
                ',
            ],
            [
                'name' => 'Inredningsdetaljer',
                'key_word' => '
                    Inredningsdetaljer                     
                ',
            ],
        ],
        'KATEGORI SÄKERHET' => [
            [
                'name' => 'Hörselskydd',
                'key_word' => '
                    Hörselskydd                     
                ',
            ],
            [
                'name' => 'Andningslarm',
                'key_word' => '
                    Andningslarm          
                ',
            ],
            [
                'name' => 'Babyvakter',
                'key_word' => '
                    Babyvakter               
                ',
            ],
            [
                'name' => 'GPS-klockor',
                'key_word' => '
                    GPS-klockor, GPS klockor                 
                ',
            ],
            [
                'name' => 'Hemsäkerhet',
                'key_word' => '
                    Hemsäkerhet            
                ',
            ],
            [
                'name' => 'Huvudskydd',
                'key_word' => '
                    hjälm, kask, Huvudskydd,
                    helmet, tin hat, basinet            
                ',
            ],
            [
                'name' => 'InredningAndSakerhet Reflexer',
                'key_word' => '
                    reflektor, märkbar, reflektion, Reflexer,
                    reflector            
                ',
            ],
            [
                'name' => 'Säkerhetsgrindar',
                'key_word' => '
                    Säkerhetsgrindar      
                ',
            ],
            [
                'name' => 'UV-tält',
                'key_word' => '
                    UV-tält                     
                '
            ],
        ]
    ];

    public function load(ObjectManager $manager)
    {
        $this->setManager($manager);

        $main = $this->createCategoryWithConf('Inredning & Säkerhet', 'Inredning, Säkerhet, Inredning Säkerhet', 'main');
        $configurations = $this->configurations;
        $this->processConfiguration($configurations, $main);
        $this->afterLoad();
    }

    public function getDependencies()
    {
        return array(
            CategoryLeksakerFixtures::class,
        );
    }
}