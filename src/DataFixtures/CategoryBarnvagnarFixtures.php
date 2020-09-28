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
                    Chassin, Barnvagnsdelar, Reservdel, Byglar, Hjul, Suffletter, 
                    Textilpaket, chassis, Stroller-Parts, Stroller Parts, spare, pram hook                     
                ',
                'sub_key_word' => [
                    'Chassin' => 'chassi, chassin',
                    'sub Byglar' => 'bygel, byglar, bumperbar, bumper bar, säkerhetsbygel, frontbygel',
                    'Hjul' => 'hjul, terränghjul, promenadhjul, 2ME slang, joggingkitt, hjulpaket, svängbara hjul, framhjul',
                    'Suffletter' => 'sufflett, sufflettpaket',
                    'syb Textilpaket' => 'färgklädsel, textilset, sommarsits, Förlängningsdelar',
                    'Ståbrädor' => 'Extra delar, Solskydd'
                ]
            ],
            [
                'name' => 'Barnvagnspaket',
                'key_word' => '
                    Joggingvagnar, Liggvagnar, Sittvagnar, Skid & Cykelvagnar, Syskonvagnar, Kombivagnar, 
                    Resevagnar & Paraplyvagnar, Barnvagnstillbehör,
                    cradle, pram, buggy
                ',
            ],
            [
                'name' => 'Duovagnar',
                'key_word' => '
                    Duovagnar, Duovagn                     
                ',
                'sub_key_word' => [
                    'sub Duovagnar' => 'duovagn'
                ]
            ],
            [
                'name' => 'Joggingvagnar',
                'key_word' => '
                    Joggingvagnar, Joggingvagn, Jogger, Joggingkit, 
                    City, walk                     
                ',
                'sub_key_word' => [
                    'Joggingvagnar/Jogger' => 'joggingvagn, jogger',
                    'Stadsvagnar' => 'stadsvagn',
                    'Promenadvagnar' => 'promenadvagn'
                ]
            ],
            [
                'name' => 'Kombivagnar',
                'key_word' => '
                    Kombivagn, universal, demi-season                     
                ',
                'sub_key_word' => [
                    'sub Kombivagnar' => 'kombivagn'
                ]
            ],
            [
                'name' => 'Liggvagnar',
                'key_word' => '
                    Liggvagn         
                ',
                'sub_key_word' => [
                    'sub Liggvagnar' => 'liggvagn'
                ]
            ],
            [
                'name' => 'Sittvagnar',
                'key_word' => '
                    Sittvagn, Sittvagnar                     
                ',
                'sub_key_word' => [
                    'sub Sittvagnar' => 'sittvagn'
                ]
            ],
            [
                'name' => 'Skid & Cykelvagnar',
                'key_word' => '
                    Skid, Cykelvagn                     
                ',
                'sub_key_word' => [
                    'sub Skid & Cykelvagnar' => 'skidvagn, cykelvagn'
                ]
            ],
            [
                'name' => 'Sulkys',
                'key_word' => '
                    Sulkys                     
                ',
                'sub_key_word' => [
                    'sub Sulkys' => 'sulky'
                ]
            ],
            [
                'name' => 'Syskonvagnar',
                'key_word' => '
                    Syskonvagn, barnvagn för två, dubbla barnvagn, dubbel,
                    double stroller, sibling stroller
                ',
                'sub_key_word' => [
                    'sub Syskonvagnar' => 'syskonvagn'
                ]
            ],
            [
                'name' => 'Multisportvagnar',
                'key_word' => '
                    sportvagnar, Multisportvagnar, sporter, sport,
                    sports, sport
                ',
                'sub_key_word' => [
                    'sub Multisportvagnar' => 'Multisportvagnar'
                ]
            ],
            [
                'name' => 'Resvagnar',
                'key_word' => '
                    Resvagnar, Resvagn
                ',
                'sub_key_word' => [
                    'sub Resvagnar' => 'Resvagnar'
                ]
            ],
        ],
        'BARNVAGNSTILLBEHÖR' => [
            [
                'name' => 'Textilpaket',
                'key_word' => '
                    färg kappa, färgklädsel, textilset, sommarsits,
                    color cloak, color, bright, upholstery
                ',
                'sub_key_word' => [
                    'Färgklädsel' => 'färg kappa, färgklädsel, textilset, sommarsits',
                ]
            ],
            [
                'name' => 'Adapter',
                'key_word' => '
                    adapter, click
                ',
                'sub_key_word' => [
                    'sub Adapter' => 'Adapter',
                ]
            ],
            [
                'name' => 'Chassi',
                'key_word' => '
                    Chassi, chassin,
                    Chassis, wheels, wheel, tire
                ',
                'sub_key_word' => [
                    'sub Chassin' => 'chassi, chassin'
                ]
            ],
            [
                'name' => 'Hjul & Däck',
                'key_word' => '
                    hjul, däck, terränghjul, promenadhjul, 2ME slang, joggingkitt, hjulpaket, svängbara hjul, framhjul,
                    wheels, wheel, tire
                ',
                'sub_key_word' => [
                    'HjulDäck' => 'hjul, däck, terränghjul, promenadhjul, 2ME slang, joggingkitt, hjulpaket, 
                    svängbara hjul, framhjul'
                ]
            ],
            [
                'name' => 'Sittdel',
                'key_word' => '
                    Sittvagn, stillasittande, sitta, Sittdel,
                    sit, sedentary
                ',
                'sub_key_word' => [
                    'sub Sittdel' => 'sittdyna, sittinlägg, komfortinlägg, minimizer'
                ]
            ],
            [
                'name' => 'Krokar',
                'key_word' => '
                    krok, galge, Krokar,
                    hook, hanger
                ',
                'sub_key_word' => [
                    'sub Krokar' => 'barnvagnskrok, krok'
                ]
            ],
            [
                'name' => 'Liggdel',
                'key_word' => '
                    bärbar, spjälsäng, bärbar-spjälsäng, Liggdel,
                    portable crib, crib, cot
                ',
                'sub_key_word' => [
                    'sub Liggdelar' => 'Liggdel, liggdelsbas'
                ]
            ],
            [
                'name' => 'Mobiler för barnvagnen',
                'key_word' => '
                    bönpåse, maraca, klocka, Mobiler för barnvagnen, Mobiler,
                    beanbag, rattle
                ',
                'sub_key_word' => [
                    'sub Mobiler' => 'mobil, speldosa'
                ]
            ],
            [
                'name' => 'Madrasskydd',
                'key_word' => '
                    madrass, Madrasskydd,
                    mattress
                ',
                'sub_key_word' => [
                    'sub Madrasskydd' => 'madrasskydd'
                ]
            ],
            [
                'name' => 'Mugghållare',
                'key_word' => '
                    Kopphållare, Mugghållare,
                    cupholder, cup holder, glass-holder
                ',
                'sub_key_word' => [
                    'sub Mugghållare' => 'Kopphållare, Mugghållare'
                ]
            ],
            [
                'name' => 'Åkpåse',
                'key_word' => '
                    Åkpåsar, Åkpåse,
                    Wholesuits, winter envelope, envelope, sleeping bag
                ',
                'sub_key_word' => [
                    'sub Åkpåsar' => 'åkpåsar, åkpåse'
                ]
            ],
            [
                'name' => 'Ståbräda',
                'key_word' => '
                    Ståbrädan, stående, Ståbräda,
                    standing board, standing-board, stand
                ',
                'sub_key_word' => [
                    'sub Ståbrädor' => 'Ståbrädan, stående, Ståbräda'
                ]
            ],
            [
                'name' => 'Sufflett',
                'key_word' => '
                    suffletten, huva, solskydd, tak, visir, mask, hjälmgaller, Sufflett, sufflettpaket,
                    roof, convertible top, hood, top, cowl, biggin
                ',
                'sub_key_word' => [
                    'sub Suffletter' => 'suffletten, huva, solskydd, tak, visir, mask, hjälmgaller, Sufflett, sufflettpaket',
                ]
            ],
            [
                'name' => 'Handvärmare',
                'key_word' => '
                    Handvärmare, handmuff, handmuffar, barnvagnsvantar,
                    glove, mittens, coupling, clutch, sleeve, socket, muff, union
                ',
                'sub_key_word' => [
                    'sub Handvärmare' => 'Handvärmare, handmuff, handmuffar, barnvagnsvantar'
                ]
            ],
            [
                'name' => 'Reflexer',
                'key_word' => '
                    reflektor, märkbar, reflektion, Reflexer,
                    reflector
                ',
                'sub_key_word' => [
                    'sub Reflexer' => 'reflektor, märkbar, reflektion, Reflexer'
                ]
            ],
            [
                'name' => 'Väderskydd & Myggnät',
                'key_word' => '
                    Väderskydd, regnskydd, raincover, solsufflett, myggnät, barnvagnsparasoll, solskydd,
                    vagnparasoll, väder kitt, insektsnät, Vindskydd,
                    parking, stand, stay, camp, quarter
                ',
                'sub_key_word' => [
                    'sub Väderskydd & Myggnät' => 'Väderskydd, regnskydd, raincover, solsufflett, myggnät,
                    barnvagnsparasoll, solskydd, vagnparasoll, väder kitt, insektsnät, Vindskydd'
                ]
            ],
            [
                'name' => 'Byglar',
                'key_word' => '
                    Bygel, Byglar
                ',
                'sub_key_word' => [
                    'sub sub Byglar' => 'bygel, byglar, bumperbar, bumper bar, säkerhetsbygel, frontbygel'
                ]
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
            'Barnvagn, Barnvagnar, Vagn, stroller',
            'main',
            'Skor, Leksaker, Bilbarnstol, Maskerad, Halloween, maskeradkläder'
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