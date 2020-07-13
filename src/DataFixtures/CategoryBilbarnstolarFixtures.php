<?php


namespace App\DataFixtures;


use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryBilbarnstolarFixtures extends AbstractFixtures implements DependentFixtureInterface
{
    /**
     * @var array
     */
    private $configurations = [
        'KATEGORI' => [
            [
                'name' => 'Babyskydd',
                'key_word' => '
                    Babyskyddet, Babyskydd
                    baby Safety                     
                ',
            ],
            [
                'name' => 'Bilbarnstolar',
                'key_word' => '
                    Bilsäten, bilstol, Bilbarnstolar,
                    car seat                     
                ',
            ],
            [
                'name' => 'Bilstolspaket',
                'key_word' => '
                    Bilstolspaket
                '
            ],
            [
                'name' => 'Bälteskuddar',
                'key_word' => '
                    kuddar, Bälteskuddar,
                    Booster Cushions, Booster               
                ',
            ],
            [
                'name' => 'Bilstolsbas',
                'key_word' => '
                    bas, Bilstolsbas    
                ',
            ],
            [
                'name' => 'Isofix',
                'key_word' => 'Isofix'
            ],
            [
                'name' => 'Isize',
                'key_word' => 'Isize'
            ],
        ],
        'TILLBEHÖR' => [
            [
                'name' => 'Adapter',
                'key_word' => '
                    Adapter  
                ',
            ],
            [
                'name' => 'Speglar',
                'key_word' => '
                    Speglar  
                ',
            ],
            [
                'name' => 'Åkpåsar',
                'key_word' => '
                    kuvertet, Åkpåse, vinter kuvert, sovsäck, Åkpåsar,
                    Wholesuits, winter envelope, envelope, sleeping bag  
                ',
            ],
            [
                'name' => 'Bilförvaring',
                'key_word' => '
                    Organize, arrangör, Bilförvaring,
                    organizer                
                ',
            ],
            [
                'name' => 'Gravidbälte',
                'key_word' => '
                    bandage, bälte, band, rem, skärp, kraftigt slag, svångrem, Gravidbälte,
                    pregnant, belt
                ',
            ],
            [
                'name' => 'Huvud och kroppsstöd',
                'key_word' => '
                    nackstöd, huvudstöd, Huvud,
                    headrest                     
                ',
            ],
            [
                'name' => 'Kopphållare och brickor',
                'key_word' => '
                    Kopphållare, mugghållare,
                    cup holder, glass-holder                     
                ',
            ],
            [
                'name' => 'Mobiler för bilstolen',
                'key_word' => '
                    hållare, innehavare, ägare, Mobiler för bilstolen, Mobiler,
                    holder, keeper       
                ',
            ],
            [
                'name' => 'Solskydd',
                'key_word' => '
                    suffletten, huva, solskydd, tak, visir, mask, hjälmgaller,
                    roof, convertible top, hood, top, cowl, biggin 
                ',
            ],
            [
                'name' => 'Väderskydd',
                'key_word' => '
                    parkering, läger, militärliv, lägerliv, tältläger, Väderskydd,
                    parking, stand, stay, camp, quarter                     
                ',
            ],
        ]
    ];

    public function load(ObjectManager $manager)
    {
        $this->setManager($manager);

        $main = $this->createCategoryWithConf('Bilbarnstolar', 'bilbarnstolar', 'main');
        $configurations = $this->configurations;
        $this->processConfiguration($configurations, $main);
        $this->afterLoad();
    }

    public function getDependencies()
    {
        return array(
            CategoryBarnvagnarFixtures::class,
        );
    }
}