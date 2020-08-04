<?php

namespace App\DataFixtures;

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
                'name' => 'Klänningar & Kjolar',
                'key_word' => '
                    Klänning, Klänningar, Tunik, Tunikor, Tröjklänning, kjol, kjolar, tunika,
                    dress, dresses, skirt, skirts, tunic, tunics, Sweater dress, Sweaterdress                     
                ',
                'sub_key_word' => [
                    'Klänningar' => 'Klänning, Klänningar, Dress, Dresses, Tyllklänningar, Tulle dresses, vardagsklänning, vardagsklänningar, Casual dresses, casualdress',
                    'Tunikor' => 'Tunik, Tunikor, Tunic, Tunics',
                    'Tröjklänningar' => 'Tröjklänning, Sweater dress, Sweaterdress',
                    'Kjolar' => 'kjol, kjolar, skirt, skirts, jeanskjol, denimskirt, jeans skirt, korta kjolar, miniskirt, mini skirt, short skirt, maxikjolar, maxikjol maxidresses, maxidress, midikjolar, midikjol, midiskirts, midiskirt, tyllkjol, tyllkjolar, tulle skirts, tulleskirt, veckade kjolar, veckad kjol, pleated skirts, pleated skirt'
                ],
                'negative_key_words' => 'skor, byxa',
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
                    'Fleecejackor' => 'Fleecejacka, Fleecejackor, Fleece jacka, Fleecejacket, Fleece jacket',
                    'Vindfleece' => 'Vindfleece, Vind fleece, Wind fleece, Windfleece',
                    'Fleecetröjor' => 'Fleecetröja, Fleece tröja',
                    'Fleecebyxor' => 'Fleecebyxor, Fleecebyxa, Fleece byxor, Fleece byxor, Fleece byxa, Fleecepants, Fleece pants, Fleece pant, Fleece trousers'
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
                    'Jeans' => 'Jeans pants, Jeansbyxa, Jeansbyxor',
                    'Sweatpants' => 'Sweatpants',
                    'Leggings' => 'Legging, Leggings',
                    'Jeggings' => 'Jeggings, Jegging',
                    'Jeansshorts' => 'Jeansshorts, Jeans shorts',
                    'Cargopants' => 'Cargopants, Cargobyxa',
                    'Termobyxor' => 'Termobyxa, Termobyxor, Thermopants, Täckbyxor, Överdragsbyxor',
                    'Skalbyxor' => 'Skalbyxor, Skalbyxa, Shellpants, Shellpant, Shell pants, Shell pant',
                    'Cargobyxor' => 'Cargobyxor, Cargobyxa, Cargopants, Cargo pants',
                    'Haremsbyxor' => 'Haremsbyxor, Harembyxa, Harem Pants, Harem Pants',
                    'Mjukisbyxor' => 'Mjukisbyxa, Mjukisbyxor, Softpants',
                    'Regnbyxor' => 'Regnbyxor, Rainpants, Rain pants',
                    'Trekvartsbyxor' => 'Trekvartsbyxa',
                    'Trekvartsbyxa' => 'Three Quarter Pants, Tre kvart byxa, Trekvartsbyxor, Trekvartsbyxa',
                    'Capribyxor' => 'Capribyxor, Capribyxa, Capripants, Capri pants',
                    'Träningsbyxor' => 'Träningsbyxa, Träningsbyxor, Sweatpants',
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
                    'Pyjamas' => 'Pyjamas, Pyjama',
                    'Morgonrockar' => 'Morgonrock',
                    'Badrockar' => 'Badrock, Bathrobe',
                    'Nattlinnen' => 'Nightgown, Nattlinne',
                    'Nattklänningar' => 'Nattklänning, Nightdress',
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
                    'Strumpor' => 'Strumpor',
                    'Strumpbyxor' => 'Strumpbyxor',
                    'Benvärmare' => 'Benvärmare',
                    'Bloomers' => 'Bloomers',
                    'Long johns' => 'Long johns',
                    'Underbyxor' => 'Underbyxor',
                    'Underklädsset' => 'Underklädsset',
                    'Underlinnen' => 'Underlinnen'
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
                        Västar, Jeansjacka, Jeansjackor, Utomhus Jacka, Streetjacka, Vardagsjacka, Softshell, Soft-Shell                     
                ',
                'negative_key_words' => 'skor, vinterstövel',
                'sub_key_word' => [
                    'Jackor' => 'Jackor',
                    'Fleece' => 'Fleece',
                    'Fleecetröjor' => 'Fleecetröjor',
                    'Fleecebyxor' => 'Fleecebyxor',
                    'Fleecejackor' => 'Fleecejackor',
                    'Fleeceoveraller' => 'Fleeceoveraller',
                    'Mössor med öronlappar' => 'Mössor med öronlappar',
                    'Mössor' => 'Mössor',
                    'Halsduk' => 'Halsduk',
                    'vantar' => 'vantar',
                    'Parkas' => 'Parkas',
                    'Regnhandskar' => 'Regnhandskar',
                    'Regnhattar' => 'Regnhattar',
                    'Regnjackor' => 'Regnjackor',
                    'Regnoveraller' => 'Regnoveraller',
                    'Regnställ' => 'Regnställ',
                    'Skaljackor' => 'Skaljackor',
                    'Skaloveraller' => 'Skaloveraller',
                    'Skidbyxor och thermobyxor' => 'Skidbyxor och thermobyxor',
                    'Skidhandskar och vantar' => 'Skidhandskar och vantar',
                    'Skidjackor' => 'Skidjackor',
                    'Skidoveraller' => 'Skidoveraller',
                    'Solhattar' => 'Solhattar',
                    'Vår och höst jackor' => 'Vår och höst jackor',
                    'Stickade halsdukar' => 'Stickade halsdukar',
                    'Ullvantar' => 'Ullvantar',
                    'Vadderade jackor' => 'Vadderade jackor',
                    'Vindjackor' => 'Vindjackor',
                    'Vinterjackor' => 'Vinterjackor',
                    'Vinteroveraller' => 'Vinteroveraller',
                    'Västar' => 'Västar',
                    'Jeansjackor' => 'Jeansjackor',
                    'Utomhus Jackor' => 'Utomhus Jackor',
                    'Streetjackor' => 'Streetjackor',
                    'Vardagsjackor' => 'Vardagsjackor',
                    'Soft-Shell' => 'Soft-Shell'
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
                    'UV-Dräkter' => 'UV-Dräkter',
                    'UV-byxoror' => 'UV-byxoror',
                    'UV-tröjor' => 'UV-tröjor',
                    'UV-set' => 'UV-set',
                    'Badbyxor' => 'Badbyxor',
                    'Badshorts' => 'Badshorts',
                    'Bikini' => 'Bikini',
                    'Baddräkter' => 'Baddräkter',
                    'Simbyxor' => 'Simbyxor',
                    'Blöjbadbyxor' => 'Blöjbadbyxor',
                    'UV-Baddräckter' => 'UV-Baddräckter',
                    'UV Badshorts' => 'UV Badshorts',
                    'Soldräkter' => 'Soldräkter',
                    'Badtröjor' => 'Badtröjor',
                    'Badrockar' => 'Badrockar',
                    'UV skjortor' => 'UV skjortor',
                    'Solskydd' => 'Solskydd'
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
                    'Nappar' =>  'Nappar, tröstnapp, sugnapp, nippel, napper,Pacifiers, dummy, pacifier, comforter, nipple',
                    'Haklappar' => 'Haklappar, haklapp, bröstlapp, Bibs, bib, feeder',
                    'Halsdukar' => 'Halsdukar, halsdukar, Neckwear',
                    'Handskar & Vantar' => 'Handskar, Vantar, vante, handske, gloves, glove, gantlet, gauntlet, tumvante',
                    'Huvudbonad' => 'Huvudbonad, hatt, Panama, keps, mössa, headdress, hat, lid, muff, dupe, pigeon, cap',
                    'Slipsar & flugor' => 'Slipsar, flugor, slips, fluga, tie, necktie, bow-tie',
                    'Skärp & Hängslen' => 'Skärp, Hängslen, bälte, rem, Belt, strap, Braces, suspenders, galluses',
                    'Väskor' => 'Väskor, påse, väska, säck, Bags, Bag, bagful, sack'
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

    public function load(ObjectManager $manager)
    {
        $this->setManager($manager);

        $main = $this->createCategoryWithConf(
            'Baby',
            'Baby, Toddler, Infant, Premature',
            'main',
            'Barn, Skor, Barnvagnar, Leksaker, Förälder'
        );
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
