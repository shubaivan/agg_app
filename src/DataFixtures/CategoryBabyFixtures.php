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
                'name' => 'Baby Klänningar & Kjolar',
                'key_word' => '
                    Klänning, Klänningar, Tunik, Tunikor, Tröjklänning, kjol, kjolar, tunika,
                    dress, dresses, skirt, skirts, tunic, tunics, Sweater dress, Sweaterdress                     
                ',
                'sub_key_word' => [
                    'Baby Klänningar' => 'Klänning, Klänningar, Dress, Dresses, Tyllklänningar, Tulle dresses, vardagsklänning, vardagsklänningar, Casual dresses, casualdress',
                    'Baby Tunikor' => 'Tunik, Tunikor, Tunic, Tunics',
                    'Baby Tröjklänningar' => 'Tröjklänning, Sweater dress, Sweaterdress',
                    'Baby Kjolar' => 'kjol, kjolar, skirt, skirts, jeanskjol, denimskirt, jeans skirt, korta kjolar, miniskirt, mini skirt, short skirt, maxikjolar, maxikjol maxidresses, maxidress, midikjolar, midikjol, midiskirts, midiskirt, tyllkjol, tyllkjolar, tulle skirts, tulleskirt, veckade kjolar, veckad kjol, pleated skirts, pleated skirt'
                ],
                'negative_key_words' => 'skor, byxa, Bloomers',
            ],
            [
                'name' => 'Bodys & Bodysuits',
                'key_word' => '
                    Body, Sparkdräkt, 
                    Romper, Footsie, Onesie, Bodysuit                     
                ',
                'sub_key_word' => [
                    'Bodys' => 'Body',
                    'Sparkdräkter' => 'Sparkdräkt, Romper, Bodysuit, Onesie, Footsie, Jumpsuit'
                ]
            ],
            [
                'name' => 'Fleecekläder',
                'key_word' => '
                    Fleecejacka, Fleece jacka, Vindfleece, Vind fleece, Fleecetröja, Fleece tröja, Fleecebyxa, Fleece byxa, 
                    Fleecejacket, Fleece jacket, Windfleece, Wind fleece, Fleecepants, Fleece pant                     
                ',
                'sub_key_word' => [
                    'Fleecekläder Fleecejackor' => 'Fleecejacka, Fleecejackor, Fleece jacka, Fleecejacket, Fleece jacket',
                    'Fleecekläder Vindfleece' => 'Vindfleece, Vind fleece, Wind fleece, Windfleece',
                    'Fleecekläder Fleecetröjor' => 'Fleecetröja, Fleece tröja',
                    'Fleecekläder Fleecebyxor' => 'Fleecebyxor, Fleecebyxa, Fleece byxor, Fleece byxor, Fleece byxa, Fleecepants, Fleece pants, Fleece pant, Fleece trousers'
                ]
            ],
            [
                'name' => 'Överdelar',
                'key_word' => '
                    T-shirt, Tee, Kortärmad, Shortsleve, tröja, tröjor, uvtröja, uv-tröja, kofta, koftor,  cardigan, cardigans, hoodie, hoodies, luvtröja, Zip-jacka, sweatshirt, sweater, sweatshirts, uv shirt, uv shirts,  Zipjacket, Zip jacket, Zip-jacket, Sweatshirtjacket, Pikétröja, Piké tröja, Rugbyskjorta, Rugbytröja, Långärmad Pikétröja,  Skjorta, Denimskjorta, Blus                     
                ',
            ],
            [
                'name' => 'Nederdelar',
                'key_word' => '
                    Byxor, Chinos, Jeans, Sweatpants, Leggings, Jeggings, jeansshorts, jeans shorts, byxor, Cargopants, 
                    Termobyxor, Termobyxa, Skalbyxor, Skalbyxa, Cargobyxor, Regnbyxa, Shell pants, Trekvartsbyxor,
                    Cargobyxa, Haremsbyxor, Harembyxa, Mjukisbyxor, Mjukisbyxa, Regnbyxor, Trekvartsbyxa, Pull up chinos,
                    Capribyxor, Capribyxa, Träningsbyxor, Träningsbyxa, Manchesterbyxor, Linne chinos, Linnechinos, Sjhorts Chinos,                               
                    Vinterstövlar, Vinterstövel, Vinter Stövlar, Vinter Stövel, Kängor, Känga, Boots, Boot, BTS stövlar,
                    Trousers, Chinos, Jeans, Sweatpants, Leggings, Jeggings, jeans shorts, jeans shorts, trousers, trousers, 
                    Cargopants, Termobyxor, Termobyxa, Shorts, Skalbyxa, Cargobyxor, Cargo Pants, Harem Pants, Harem Pants, Soft Pants, 
                    Soft Pants, Rain Pants, Rain Pants, Shell Pants, Three Quarter Pants, Three Quarter Pants, Capri Pants, 
                    Capri Pants, Sweatpants, Sweatshorts, Manchesterpants, Linnen chinos, Linnenchinos, Pull-up chinos, Shorts chinos                     
                ',
                'sub_key_word' => [
                    'Byxor' => 'Byxor, Byxa, Trousers, Troucer',
                    'Chinos' => 'Chinos, Linnechinos, Linne Chinos',
                    'Nederdelar Jeans' => 'Jeans pants, Jeansbyxa, Jeansbyxor',
                    'Sweatpants' => 'Sweatpants',
                    'Baby Nederdelar Leggings' => 'Legging, Leggings',
                    'Jeggings' => 'Jeggings, Jegging',
                    'Jeansshorts' => 'Jeansshorts, Jeans shorts',
                    'Cargopants' => 'Cargopants, Cargobyxa',
                    'Termobyxor' => 'Termobyxa, Termobyxor, Thermopants, Täckbyxor, Överdragsbyxor',
                    'Överdelar Skalbyxor' => 'Skalbyxor, Skalbyxa, Shellpants, Shellpant, Shell pants, Shell pant',
                    'Nederdelar Cargobyxor' => 'Cargobyxor, Cargobyxa, Cargopants, Cargo pants',
                    'Haremsbyxor' => 'Haremsbyxor, Harembyxa, Harem Pants, Harem Pants',
                    'Mjukisbyxor' => 'Mjukisbyxa, Mjukisbyxor, Softpants',
                    'Regnbyxor' => 'Regnbyxor, Rainpants, Rain pants',
                    'Trekvartsbyxor' => 'Trekvartsbyxa',
                    'Trekvartsbyxa' => 'Three Quarter Pants, Tre kvart byxa, Trekvartsbyxor, Trekvartsbyxa',
                    'Capribyxor' => 'Capribyxor, Capribyxa, Capripants, Capri pants',
                    'Nederdelar Träningsbyxor' => 'Träningsbyxa, Träningsbyxor, Sweatpants',
                    'Pull-up byxor' => 'Pull-upp byxor, Pull upp byxor, Pull-up pants, Pull upp pants',
                    'Linnebyxor' => 'Linnebyxor, Linnebyxa, Linnen pants, Linnen pants',
                    'Manchesterbyxor' => 'Manchesterbyxor, Manchesterbyxa, Manchesterpants, Manchester pants',
                    'Shorts' => 'Shorts, Kortbyxor, Kortbyxa'
                ],
                'negative_key_words' => 'skor, byxa, snutte, tossor',
            ],
            [
                'name' => 'Sovkläder',
                'key_word' => '
                    Pyjamas, Morgonrock, Badrockar, Badrock, Nattlinne, Nattklänning,
                    Pajamas, Bathrobe, Bathrobes, Nightdress                     
                ',
                'sub_key_word' => [
                    'Sovkläder Pyjamas' => 'Pyjamas, Pyjama',
                    'Sovkläder Morgonrockar' => 'Morgonrock',
                    'Sovkläder Badrockar' => 'Badrock, Bathrobe',
                    'Sovkläder Nattlinnen' => 'Nightgown, Nattlinne',
                    'Sovkläder Nattklänningar' => 'Nattklänning, Nightdress',
                ]
            ],
            [
                'name' => 'Baby Underkläder',
                'key_word' => '
                    strumpor, strumpbyxor, Benvärmare, Bloomers, Boxershorts, Long johns, Strumpbyxor, Strumpor, 
                    Underbyxor, Underbyxa, Underklädsset, Underlinnen, Underlinne, Strumpbyxa,
                    Underwear, stockings, tights, leg warmers, bloomers, long johns, tights, 
                    tights, stockings, Socks, Pantyhose, Pantyhose Underwear set, Underliners, Underliners                     
                ',
                'negative_key_words' => 'skor, byxa, snutte, tossor,  babyskor',
                'sub_key_word' => [
                    'Baby Underkläder Strumpor' => 'Strumpor',
                    'Baby Underkläder Strumpbyxor' => 'Strumpbyxor',
                    'Baby Underkläder Benvärmare' => 'Benvärmare',
                    'Baby Underkläder Bloomers' => 'Bloomers',
                    'Baby Underkläder Long johns' => 'Long johns',
                    'Baby Underkläder Underbyxor' => 'Underbyxor',
                    'Baby Underkläder Underklädsset' => 'Underklädsset',
                    'Baby Underkläder Underlinnen' => 'Underlinnen'
                ]
            ],
            [
                'name' => 'Baby Ytterkläder',
                'key_word' => '
                        Jacka, Fleece, Fleece tröjor, Fleecetröja, Fleece tröja, Fleecebyxa, Fleece byxa, Fleece byxor, Fleecebyxor, Fleecejackor, 
                        Fleeceoveraller, Fleecetröjor, Mössa med öronlappar, Mössa, halsduk och vantar, Mössor, Parkas, Regnhandskar, 
                        Regnhatt, Regnjackor, Regnoveraller, Regnställ, Skaljackor, Regnjacka, Skaloveraller, Skid och thermobyxor, 
                        Skidhandskar och vantar, Skidjackor, Skidjacka, Skidoveraller, Skidoverall, Solhatt, Vår och höst jackor, 
                        Stickade halsdukar, Stickad halsduk, Ullvantar, Vadderade jackor, Vindjackor, Vinterjackor, Vinteroveraller, 
                        Västar, Jeansjacka, Jeansjackor, Utomhus Jacka, Streetjacka, Vardagsjacka, Softshell, Soft-Shell, Regnjackor                     
                ',
                'negative_key_words' => 'skor, vinterstövel',
                'sub_key_word' => [
                    'Baby Ytterkläder Jackor' => 'Jackor',
                    'Baby Ytterkläder Fleece' => 'Fleece',
                    'Baby Ytterkläder Fleecetröjor' => 'Fleecetröjor',
                    'Baby Ytterkläder Fleecebyxor' => 'Fleecebyxor',
                    'Baby Ytterkläder Fleecejackor' => 'Fleecejackor',
                    'Baby Ytterkläder Fleeceoveraller' => 'Fleeceoveraller',
                    'Baby Ytterkläder Mössor med öronlappar' => 'Mössor med öronlappar',
                    'Baby Ytterkläder Mössor' => 'Mössor',
                    'Baby Ytterkläder Halsduk' => 'Halsduk',
                    'Baby Ytterkläder Vantar' => 'vantar',
                    'Baby Ytterkläder Parkas' => 'Parkas',
                    'Baby Ytterkläder Regnhandskar' => 'Regnhandskar',
                    'Baby Ytterkläder Regnhattar' => 'Regnhattar',
                    'Baby Ytterkläder Regnjackor' => 'Regnjackor, Regnjacka',
                    'Baby Ytterkläder Regnoveraller' => 'Regnoveraller',
                    'Baby Ytterkläder Regnställ' => 'Regnställ',
                    'Baby Ytterkläder Skaljackor' => 'Skaljackor',
                    'Baby Ytterkläder Skaloveraller' => 'Skaloveraller',
                    'Baby Ytterkläder Skidbyxor och thermobyxor' => 'Skidbyxor och thermobyxor',
                    'Baby Ytterkläder Skidhandskar och vantar' => 'Skidhandskar och vantar',
                    'Baby Ytterkläder Skidjackor' => 'Skidjackor',
                    'Baby Ytterkläder Skidoveraller' => 'Skidoveraller',
                    'Baby Ytterkläder Solhattar' => 'Solhattar',
                    'Baby Ytterkläder Vår och höst jackor' => 'Vår och höst jackor',
                    'Baby Ytterkläder Stickade halsdukar' => 'Stickade halsdukar',
                    'Baby Ytterkläder Ullvantar' => 'Ullvantar',
                    'Baby Ytterkläder Vadderade jackor' => 'Vadderade jackor',
                    'Baby Ytterkläder Vindjackor' => 'Vindjackor',
                    'Baby Ytterkläder Vinterjackor' => 'Vinterjackor',
                    'Baby Ytterkläder Vinteroveraller' => 'Vinteroveraller',
                    'Baby Ytterkläder Västar' => 'Västar',
                    'Baby Ytterkläder Jeansjackor' => 'Jeansjackor',
                    'Baby Ytterkläder Utomhus Jackor' => 'Utomhus Jackor',
                    'Baby Ytterkläder Streetjackor' => 'Streetjackor',
                    'Baby Ytterkläder Vardagsjackor' => 'Vardagsjackor',
                    'Baby Ytterkläder Soft-Shell' => 'Soft-Shell'
                ]
            ],
            [
                'name' => 'Baby UV & Bad',
                'key_word' => '
                    UV-Dräkt, UV-byxor, UV-byxa, uvtröja, uv-tröja, uv-tröjor, uvtröjor, UV-set, Badbyxor, Badbyxa, Badshorts, Bikini, 
                    Swimsuit, Swim Suit, Swimpants, Swim Pants, Swim Diapers, Blöjbadbyxor, Blöjbadbyxa, UV-Baddräckt, 
                    UV Badshorts, UV-Badshorts, Sunsuits, Sunsuit, Badtröja, Bad tröja, Badrockar, Badrock, UV shirt, UV-shirt, Solskydd,
                    UV Apparel, UV Pants, UV Pants, Sweatshirt, UV Sweater, UV Sweatshirts, Sweatshirts, UV Sets, Swimsuit, 
                    Swimwear, Swimwear, Bikini, Swimsuit, Swim Suit, Swimpants, Swim Pants, Swim Diapers, Diaper Tights, 
                    Diaper Tights, UV Bathing Suits, UV Bathing Shorts, UV Bathing Shorts, Sunsuits, Sunsuit, Sweater, 
                    Bathing Sweater, Bathing Suits, Bathrobe                     
                ',
                'sub_key_word' => [
                    'Baby UV-Dräkter' => 'UV-Dräkter',
                    'Baby UV-byxoror' => 'UV-byxoror',
                    'Baby UV-tröjor' => 'UV-tröjor',
                    'Baby UV-set' => 'UV-set',
                    'Baby Badbyxor' => 'Badbyxor',
                    'Baby Badshorts' => 'Badshorts',
                    'Baby Bikini' => 'Bikini',
                    'Baby Baddräkter' => 'Baddräkter',
                    'Baby Simbyxor' => 'Simbyxor',
                    'Baby Blöjbadbyxor' => 'Blöjbadbyxor',
                    'Baby UV-Baddräckter' => 'UV-Baddräckter',
                    'Baby UV Badshorts' => 'UV Badshorts',
                    'Baby Soldräkter' => 'Soldräkter',
                    'Baby Badtröjor' => 'Badtröjor',
                    'Baby Badrockar' => 'Badrockar',
                    'Baby UV skjortor' => 'UV skjortor',
                    'Baby Solskydd' => 'Solskydd'
                ]
            ],
            [
                'name' => 'Baby Accessoarer',
                'key_word' => '
                    Skötväska, Överdrag, Napp, Skötväska, Skötmatta, Ammningskudde, Bib, Beuty Bag, Changing Bag, Changing Mat, 
                    Apron, Nursing Pillow, Nappy, Wipes, Changing Cushion, Haklappar, Långärmade Haklappar, Cooler Bag, Ice Packs, 
                    Squeeze Bags, Pacifiers, Straw Cup, Napphållare, Nappflaska, Dinapp, Nappar, Pipar, Badmattor, Straw Bottle, Flaskborste, 
                    Babysitter, Bärsele, Väska, Lekhage, Barngrind, Baby Scarf, Babyscarf, Ryggväska, Boostersinlägg, Bitleksak, 
                    Hörselskydd, Bältesväska, Skötbädd, Resväska, Bitring, Klämmisar, Pottstol, Potta, Barnpall, Blöjhink, Sele, Reflexväst, 
                    Adapter, Cykelsits, Flaska, Bottle, Transportväska, Leksaksbåge, Flytväst, Babyvakt, Hoppgunga, Flexi Bath, Sittdyna, 
                    Tvättlappar, Tillmatningsset, Nässug, Räddningsväst, Cykelstol, Mammoth Väska, Back carrier, Baby Watch, Spisskydd, 
                    Knappskydd, Bärsjal, Barnstol, Bouncer, Diinglisar, Badbalja, Rörelselarm, Lås, Shower, Axelremmsskydd, 
                    Spädbarnsinlägg, Nässpray, Bitkudde, Vattenskydd, Badmatta, Smaktestare, Hårborste, Ryggsäck, Grind, Resesäng, 
                    Buddiezzz, Bärryggsäck, Termometer, Videobabyvakt, Pulverbehållare, Badstol, Flaskvärmare, Pipmugg, Babymonitor, 
                    Babylarm, Matnings set, Luftrenare, Flaskvärmare, Dryckesmugg, Tröstplåster, Toalettsits, Badkar, Stickkontaktplugg, 
                    Förvaringsmugg, Hörnskydd, Fönsterlås, Våtservett                      
                ',
                'sub_key_word' => [
                    'Baby Nappar' =>  'Nappar, tröstnapp, sugnapp, nippel, napper,Pacifiers, dummy, pacifier, comforter, nipple',
                    'Baby Haklappar' => 'Haklappar, haklapp, bröstlapp, Bibs, bib, feeder',
                    'Baby Accessoarer Halsdukar' => 'Halsdukar, halsdukar, Neckwear',
                    'Baby Handskar & Vantar' => 'Handskar, Vantar, vante, handske, gloves, glove, gantlet, gauntlet, tumvante',
                    'Baby Huvudbonad' => 'Huvudbonad, hatt, Panama, keps, mössa, headdress, hat, lid, muff, dupe, pigeon, cap',
                    'Baby Slipsar & flugor' => 'Slipsar, flugor, slips, fluga, tie, necktie, bow-tie',
                    'Baby Skärp & Hängslen' => 'Skärp, Hängslen, bälte, rem, Belt, strap, Braces, suspenders, galluses',
                    'Baby Accessoarer Väskor' => 'Väskor, påse, väska, säck, Bags, Bag, bagful, sack'
        ]
            ],
        ],
        'ACCESSOARER' => [
            [
                'name' => 'Nappar',
                'key_word' => '
                    Nappar, tröstnapp, sugnapp, nippel, napper,
                    Pacifiers, dummy, pacifier, comforter, nipple        
                ',
                'negative_key_words' => 'cykelsits, comfort',
            ],
            [
                'name' => 'Haklappar',
                'key_word' => '
                    Haklappar, haklapp, bröstlapp,
                    Bibs, bib, feeder        
                ',
            ],
            [
                'name' => 'Halsdukar',
                'key_word' => '
                    Halsdukar, halsdukar,
                    Neckwear
                ',
            ],
            [
                'name' => 'Handskar & Vantar',
                'key_word' => '
                    Handskar, Vantar, vante, handske,
                    gloves, glove, gantlet, gauntlet, tumvante        
                ',
            ],
            [
                'name' => 'Huvudbonad',
                'key_word' => '
                    Huvudbonad, hatt, Panama, keps, mössa, 
                    headdress, hat, lid, muff, dupe, pigeon, cap        
                ',
            ],
            [
                'name' => 'Slipsar & flugor',
                'key_word' => '
                    Slipsar, flugor, slips, fluga,
                    tie, necktie, bow-tie   
                ',
            ],
            [
                'name' => 'Skärp & Hängslen',
                'key_word' => '
                    Skärp, Hängslen, bälte, rem        
                ',
                'negative_key_words' => 'hängselbyxor',
            ],
            [
                'name' => 'Baby Väskor',
                'key_word' => '
                    Väskor, påse, väska, säck,
                    Bags, Bag, bagful, sack        
                ',
                'negative_key_words' => 'skallerljud, resestol, Badkar, Skedar, diskborste, portionspåsar, Korbell, byggklossar, UBBI, bok',
            ],
        ]
    ];

    private $configurationsSize = [
        'KLÄDER 50-86 CL (0-1 ÅR)' => [
            'Flicka' => [
                'sizes' => '50,56,62,68,74,80,86',
                'positive_key_words' => 'girl',
            ],
            'Pojke' => [
                'sizes' => '50,56,62,68,74,80,86',
                'positive_key_words' => 'boy',
            ]   
        ]
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

        $this->processSizeCategories($this->configurationsSize, $main);

        $this->afterLoad();
    }

    public function getDependencies()
    {
        return array(
            CategorySkorFixtures::class,
        );
    }
}
