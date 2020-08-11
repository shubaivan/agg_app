<?php


namespace App\DataFixtures;


use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryBarnvagnarFixtures extends AbstractFixtures implements DependentFixtureInterface
{
    /**
     * @var array
     */
    private $configurations = [
        'KATEGORI' => [
            [
                'name' => 'Barnvagnsdelar & Chassin',
                'key_word' => '
                    Duovagnar, Chassin, Barnvagnsdelar, reservdel,  barnvagn,
                    chassis, Stroller-Parts, Stroller Parts, spare, pram hook                     
                ',
            ],
            [
                'name' => 'Barnvagnspaket',
                'key_word' => '
                    vagga, pråm, Barnvagnspaket,
                    cradle, pram, buggy                     
                ',
            ],
            [
                'name' => 'Duovagnar',
                'key_word' => '
                    Duovagnar                     
                ',
            ],
            [
                'name' => 'Joggingvagnar',
                'key_word' => '
                    Joggingvagnar, Joggingvagn, Jogger, stad, promenad, 
                    City, walk                     
                ',
            ],
            [
                'name' => 'Kombivagnar',
                'key_word' => '
                    universell, oavsett årstid, årstid,
                    universal, demi-season                     
                ',
            ],
            [
                'name' => 'Liggvagnar',
                'key_word' => '
                    Liggvagnar         
                ',
            ],

            [
                'name' => 'Sittvagnar',
                'key_word' => '
                    Sittvagn, stillasittande, sitta, Sittvagnar,
                    sit, sedentary                     
                ',
            ],
            [
                'name' => 'Skid - & Cykelvagnar',
                'key_word' => '
                    Skid, Cykelvagnar, Cykel, skidåkning, hoj,
                    Bicycle, skiing, ski, bike, cycle                     
                ',
            ],
            [
                'name' => 'Sulkys',
                'key_word' => '
                    Sulkys                     
                ',
            ],
            [
                'name' => 'Syskonvagnar',
                'key_word' => '
                    Syskonvagnar, barnvagn för två, dubbla barnvagn, dubbel,
                    double stroller, double, siblings                     
                ',
            ],
            [
                'name' => 'Åkpåsar',
                'key_word' => '
                    kuvertet, Åkpåsar, vinter kuvert, sovsäck,
                    Wholesuits, winter envelope, envelope, sleeping bag                     
                ',
            ],
            [
                'name' => 'Multisportvagnar',
                'key_word' => '
                    sportvagnar, Multisportvagnar, sporter, sport,
                    sports, sport                     
                ',
            ],
        ],
        'BARNVAGNSTILLBEHÖR' => [
            [
                'name' => 'Färgklädsel',
                'key_word' => '
                    färg kappa, färg, ljus, färgklädsel,
                    color cloak, color, bright, upholstery
                ',
            ],
            [
                'name' => 'Adapter',
                'key_word' => '
                    adapter, click
                ',
            ],
            [
                'name' => 'Reservdelar',
                'key_word' => '
                    Reservdelar, reservdel, detalj,
                    Spare, spares, detail, part
                ',
            ],
            [
                'name' => 'Chassi',
                'key_word' => '
                    Chassi, hjul, däck,
                    Chassis, wheels, wheel, tire
                ',
            ],
            [
                'name' => 'Resvagnar',
                'key_word' => '
                    turism, turist, Resvagnar,
                    tourist, tourism
                ',
            ],
            [
                'name' => 'Hjul',
                'key_word' => '
                    hjul, däck,
                    wheels, wheel, tire
                ',
            ],
            [
                'name' => 'Sittdel',
                'key_word' => '
                    Sittvagn, stillasittande, sitta, Sittdel,
                    sit, sedentary
                ',
            ],
            [
                'name' => 'Krokar',
                'key_word' => '
                    krok, galge, Krokar,
                    hook, hanger
                ',
            ],
            [
                'name' => 'Liggdel',
                'key_word' => '
                    bärbar, spjälsäng, bärbar-spjälsäng, Liggdel,
                    portable crib, crib, cot
                ',
            ],
            [
                'name' => 'Mobiler för barnvagnen',
                'key_word' => '
                    bönpåse, maraca, klocka, Mobiler för barnvagnen, Mobiler,
                    beanbag, rattle
                ',
            ],
            [
                'name' => 'Sittdynor',
                'key_word' => '
                    Sittdyna, rullstolskudde, Sittdynor
                ',
            ],
            [
                'name' => 'Madrasskydd',
                'key_word' => '
                    madrass, Madrasskydd,
                    mattress
                ',
            ],
            [
                'name' => 'Mugghållare',
                'key_word' => '
                    Kopphållare, Mugghållare,
                    cupholder, cup holder, glass-holder
                ',
            ],
            [
                'name' => 'Åkpåse',
                'key_word' => '
                    kuvertet, Åkpåsar, vinter kuvert, sovsäck, Åkpåse,
                    Wholesuits, winter envelope, envelope, sleeping bag
                ',
            ],
            [
                'name' => 'Ståbräda',
                'key_word' => '
                    Ståbrädan, stående, Ståbräda,
                    standing board, standing-board, stand
                ',
            ],
            [
                'name' => 'Sufflett',
                'key_word' => '
                    suffletten, huva, solskydd, tak, visir, mask, hjälmgaller, Sufflett,
                    roof, convertible top, hood, top, cowl,biggin
                ',
            ],
            [
                'name' => 'Handvärmare',
                'key_word' => '
                    koppling, handske, vantar, händerna, Handvärmare,
                    glove, mittens, coupling, clutch, sleeve, socket, muff, union
                ',
            ],
            [
                'name' => 'Reflexer',
                'key_word' => '
                    reflektor, märkbar, reflektion, Reflexer,
                    reflector
                ',
            ],
            [
                'name' => 'Väderskydd',
                'key_word' => '
                    parkering, läger, militärliv, lägerliv, tältläger, Väderskydd,
                    parking, stand, stay, camp, quarter
                ',
            ],
        ],
        'VÄSKOR' => [
            [
                'name' => 'Skötväskor',
                'key_word' => '
                    blöjor, rombiskt mönster, Skötväskor,
                    diapers, nappy, napkin
                ',
            ],
            [
                'name' => 'Transportväskor',
                'key_word' => '
                    transport, Transportväskor,
                    transportation
                ',
            ],
            [
                'name' => 'Organizer',
                'key_word' => '
                    arrangör,
                    organizer
                ',
            ],
        ]
    ];

    public function load(ObjectManager $manager)
    {
        $this->setManager($manager);

        $main = $this->createCategoryWithConf(
            'Barnvagnar',
            'Barnvagn, Barnvagnar, Vagn',
            'main',
            'Skor, Leksaker, Bilbarnstol, Maskerad'
        );
        $configurations = $this->configurations;
        $this->processConfiguration($configurations, $main);
        $this->afterLoad();
    }

    public function getDependencies()
    {
        return array(
            CategoryBabyFixtures::class,
        );
    }
}