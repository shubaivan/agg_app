<?php


namespace App\DataFixtures;


use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryLeksakerFixtures extends AbstractFixtures implements DependentFixtureInterface
{
    /**
     * @var array
     */
    private $configurations = [
        'KATEGORI' => [
            [
                'name' => 'Adventskalendrar',
                'key_word' => '
                    Advents kalendar, kalendar, Adventskalendrar                
                ',
            ],
            [
                'name' => 'Lego',
                'key_word' => '
                    Lego          
                ',
                'negative_key_words' => 't-shirt, shorts',
            ],
            [
                'name' => 'Babylek',
                'key_word' => '
                    Babygym, Babylek
                ',
            ],
            [
                'name' => 'Leksaksdjur',
                'key_word' => '
                    djur, kreatur, Leksaksdjur,
                    Animals
                ',
            ],
            [
                'name' => 'Barnböcker',
                'key_word' => '
                    bok, tinga, boka, notera, historia, berättelse, sägen, nyhetsstoff, våning, Barnböcker,
                    book, volume, psalterium, story
                ',
            ],
            [
                'name' => 'Leksaksfordon',
                'key_word' => '
                    fordon, transport, farkost, uttrycksmedel, bindemedel, åkdon, Leksaksfordon,
                    vehicle, truck
                ',
            ],
            [
                'name' => 'Barnkalas och Party',
                'key_word' => '
                    Barnkalas, parti, fest, patrull, samkväm, hippa,
                    Party, fest, part
                ',
            ],
            [
                'name' => 'Lektält och Tunnlar',
                'key_word' => '
                    Tunnlar, Lektält, tält, stort tält, markis, tunnel, underjordisk gång, förbindelsegång,
                    play tent, tent, marquee, booth
                ',
            ],
            [
                'name' => 'Byggleksaker',
                'key_word' => '
                    byggarbetsplats, construction, uppförande,
                    building, developing
                ',
            ],
            [
                'name' => 'Maskerad och Utklädning',
                'key_word' => '
                    teater, Maskerad, Utklädning, maskeraddräkt, karneval, trasa, rag, bråk, karneval, upptåg, trasmatta,
                    Masquerade, mask, costume, Carnival
                ',
            ],
            [
                'name' => 'Dockhuslek',
                'key_word' => '
                    Dockhus, hydda,
                    Dollhouse 
                ',
            ],
            [
                'name' => 'Minihem och Rollspel',
                'key_word' => '
                    Minihem, Rollspel,
                    role-playing games                     
                ',
            ],

            [
                'name' => 'Minihem och Rollspel',
                'key_word' => '
                    Minihem, Rollspel,
                ',
            ],
            [
                'name' => 'Docklek',
                'key_word' => '
                    Docklek                     
                ',
            ],
            [
                'name' => 'Musik och Sång',
                'key_word' => '
                    Musik, Sång                     
                ',
            ],
            [
                'name' => 'Experiment och Vetenskap',
                'key_word' => '
                    Experiment, Vetenskap                 
                ',
            ],
            [
                'name' => 'Playmobil',
                'key_word' => '
                    Playmobil                     
                ',
            ],
            [
                'name' => 'Figurer och Lekset',
                'key_word' => '
                    Figurer, Lekset              
                ',
            ],
            [
                'name' => 'Pussel',
                'key_word' => '
                    Pussel                     
                ',
            ],
            [
                'name' => 'Gosedjur',
                'key_word' => '
                    Gosedjur                     
                ',
            ],
            [
                'name' => 'Pyssla,Rita och Skriva',
                'key_word' => '
                    Minihem, Rollspel                   
                ',
                'negative_key_words' => 'Inredning, tabell, väskset, Skrivbord, bord',
            ],
            [
                'name' => 'Gungdjur och Käpphästar',
                'key_word' => '
                    Gungdjur, Käpphästar         
                ',
            ],
            [
                'name' => 'Samlarprodukter',
                'key_word' => '
                    Samlarprodukter                     
                ',
            ],
            [
                'name' => 'Interaktivt och Radiostyrt',
                'key_word' => '
                    Interaktivt, Radiostyrt, fjärrkontroll, fjärrmanövrering,
                    remote
                ',
            ],
            [
                'name' => 'Spel',
                'key_word' => '
                    Brädspel, backgammon, Spel,
                    Board games, Board game            
                ',
                'negative_key_words' => 'sandaler, tennisskor, speldosa',
            ],
            [
                'name' => 'Labyrinter och Kulramar',
                'key_word' => '
                    Labyrinter, Kulramar, Labyrinths,
                    maze, labyrinth      
                ',
            ],
            [
                'name' => 'Åkfordon',
                'key_word' => '
                    Ridfordon, ridning, skrivmaskin, motorcykel, Åkfordon,
                    Riding Vehicles, Riding
                ',
            ],
            [
                'name' => 'Väskor',
                'key_word' => '
                    Väskor      
                ',
            ],
            [
                'name' => 'Utelek',
                'key_word' => '
                    leka utomhus, boll, frisbee, Utelek      
                ',
            ],
        ],
    ];

    public function load(ObjectManager $manager)
    {
        $this->setManager($manager);

        $main = $this->createCategoryWithConf('Leksaker', 'leksaker', 'main');
        $configurations = $this->configurations;
        $this->processConfiguration($configurations, $main);
        $this->afterLoad();
    }

    public function getDependencies()
    {
        return array(
            CategoryForalderFixtures::class,
        );
    }
}