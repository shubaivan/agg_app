<?php


namespace App\DataFixtures;


use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryForalderFixtures extends AbstractFixtures implements DependentFixtureInterface
{
    /**
     * @var array
     */
    private $configurations = [
        'MAMMA' => [
            [
                'name' => 'Amningsinlägg',
                'key_word' => '
                    amning, skötsel, Amningsinlägg,
                    nursing, breast-feeding                
                ',
            ],
            [
                'name' => 'Amningskuddar och Gravidkuddar',
                'key_word' => '
                    nackstöd, huvudstöd, Amningskuddar, Gravidkuddar,
                    headrest          
                ',
            ],
            [
                'name' => 'Amningsponchos',
                'key_word' => '
                    amningsponcho, Amningsponchos,
                    breastfeeding, poncho               
                ',
            ],
            [
                'name' => 'Bröstpumpar och Tillbehör',
                'key_word' => '
                    bröstpump, mjölkbehållare, Bröstpumpar, Tillbehör,
                    breast pumps, breast-pumps
                ',
            ],
            [
                'name' => 'Hud & Bröstvård',
                'key_word' => '
                    kräm, grädde, grädda, krämfärg, vård, omsorg, skötsel, försiktighet, vara, Bröstvård,
                    cream, care, aktivkräm
                ',
            ],
            [
                'name' => 'Intim och Kosttillskott',
                'key_word' => '
                    Intim, Kosttillskott, tillägg,
                    supplements, addition
                ',
            ],
            [
                'name' => 'Mammakläder',
                'key_word' => '
                    Moderskap, Mammakläder,
                    Maternity
                ',
            ],
            [
                'name' => 'Gravidsmycken',
                'key_word' => '
                    gravida, smycken, Gravidsmycken,
                    pregnant Jewelry, pregnant, Jewelry            
                ',
            ],
        ],
        'MAMMA & PAPPA' => [
            [
                'name' => 'Bärselar',
                'key_word' => '
                    sling, slunga, slungande, gevärsrem, bärsele, mitella, Bärselar,
                    Baby Carriers, harness, harness, looping, belt for rifle, kangaroo, mitella
                ',
            ],
            [
                'name' => 'Bärsjalar',
                'key_word' => '
                    sling, slunga, Bärsjalar, Bärsjal,
                    Baby Slings, Baby-Slings, Slings       
                ',
            ],
            [
                'name' => 'Stödbälten och Gördlar',
                'key_word' => '
                    bälte, skärp, bandage, Stödbälten, Gördlar,
                    Support Belts, Belts, belt 
                ',
            ],
            [
                'name' => 'Gravidböcker och Babyalbum',
                'key_word' => '
                    Böcker om graviditet, Gravidböcker, Babyalbum,
                    Pregnancy books, Baby albums                     
                ',
            ],
        ]
    ];

    public function load(ObjectManager $manager)
    {
        $this->setManager($manager);

        $main = $this->createCategoryWithConf(
            'Förälder',
            'förälder, accessoarer',
            'main'
        );
        $configurations = $this->configurations;
        $this->processConfiguration($configurations, $main);
        $this->afterLoad();
    }

    public function getDependencies()
    {
        return array(
            CategoryBilbarnstolarFixtures::class,
        );
    }
}