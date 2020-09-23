<?php


namespace App\DataFixtures;


use Doctrine\Persistence\ObjectManager;

class CategoryKladerFixtures extends AbstractFixtures
{
    /**
     * @var array
     */
    private $configurations = [
        'KATEGORI' => [
            [
                'name' => 'T-shirts',
                'key_word' => '
                    t-shirt, kortärmad, tee, shortsleve, piké, pike, långärmad t-shirt, Rugbytröja, Rugby tröja,
                    t-shirt, short-sleeved, tee, shortsleeve, piké, long-sleeved t-shirt, T-shirten                     
                ',
                'negative_key_words' => 'dress, shoes, tunika',
                'sub_key_word' => [
                    'T-shirt' => 'T-shirt, Kortärmad, Tee, Shortsleeve, short sleeve, short sleeved',
                    'Pikéer' => 'Piké, Rugbytröja, Rugby tröja',
                    'Långärmade t-shirts' => 'Långärmad t-shirt'
                ]
            ],
            [
                'name' => 'Tröjor & koftor',
                'key_word' => '
                    tröja, tröjor, kofta, koftor, cardigan, cardigans, hoodie, hoodies, luvtröja, Zip-jacka, huvtröja, luvtröja, luvtröjor, collegetröja,
                    sweatshirt, sweater, sweatshirts, Zipjacket, Zip jacket, Zip-jacket, Sweatshirtjacket
                ',
                'negative_key_words' => 'Dress, pant, pants, shorts, Skirt, tee, väggkrok, tröja, Tunika, Topp, sweatshirtshorts',
                'sub_key_word' => [
                    'Tröjor' => 'Tröja, Tröjor, Sweatshirt, Sweater, Sweatshirts, collegetröja, collegetröjor',
                    'Koftor' => 'Kofta, Koftor',
                    'Cardigans' => 'Cardigan, Cardigans',
                    'Luvtröjor' => 'Luvtröja, Luvtröjor, Hoodie, Hoodies',
                    'Zipjackor' => 'Zipjacka, Zipjackor, Zip jacka, Zip jackor, Zipjacket, Zip jacket, Zip-jacket'
                ]
            ],
            [
                'name' => 'Skjortor & Blusar',
                'key_word' => '
                    skjorta, skjortor, blus, blusar,
                    shirt, shirts, blouse, blouses
                ',
                'sub_key_word' => [
                    'Skjortor' => 'Skjorta, Skjortor, Shirt, Shirts',
                    'Blusar' => 'Blus, Blusar, Blouse, Blouses'
                ],
            ],
            [
                'name' => 'Byxor & shorts',
                'key_word' => '
                    Byxor, Chinos, Jeans, Sweatpants, Leggings, Jeggings, shorts, byxa, Cargopants, Skalbyxor, Skalbyxa, Cargobyxor, 
                    Cargobyxa, Haremsbyxor, Harembyxa, Mjukisbyxor, Mjukisbyxa, Trekvartsbyxor, Trekvartsbyxa, Capribyxor, Capribyxa, Träningsbyxor, 
                    Träningsbyxa, Bloomers, Jeansshorts, Jeans shorts, Shell pants, Pull up chinos, Pull up chino, Manchesterbyxor, Linne chinos, Linnechinos, Shorts chinos, Shorts,
                    Trousers, Sweatpants, Cargopants, Shorts, Skalbyxa, 
                    Cargobyxor, Pants, Sweatpants, Sweatshorts    
                ',
                'sub_key_word' => [
                    'Chinos' => 'Chinos',
                    'Jeans' => 'Jeans',
                    'Mjukisbyxor' => 'Mjukisbyxa, Mjukisbyxor, Sweatpants, Sweatshorts',
                    'Leggings' => 'Leggings',
                    'Jeggings' => 'Jeggings',
                    'Jeansshorts' => 'Jeansshorts, Jeans shorts',
                    'Cargobyxor' => 'Cargopants, Cargobyxor',
                    'Skalbyxor' => 'Skalbyxa, Skalbyxor, Shell-pants, Shell pants, Shellpants',
                    'Haremsbyxor' => 'Harembyxa, Harembyxor, Haremtrousers, Harem trousers',
                    'Trekvartsbyxor' => 'Trekvartsbyxor, Three Quarter Pants',
                    'Capribyxor' => 'Capribyxa, Capribyxor, Capripants, Capri pants',
                    'Träningsbyxor' => 'Träningsbyxa, Träningsbyxor, Workoutpants, Workout pants, Training pants, Exercisepants, Exercise pants',
                    'Manchesterbyxor' => 'Manchesterbyxa',
                    'Shorts' => 'Shorts'
                ],
                'negative_key_words' => '
                    Emmaljunga, babysitter, muslin, Linne, Linnes, strumpor, Jackan, kofta, t-shirt, tröja, väggkrok, barnvagnar
                ',
            ],
            [
                'name' => 'Underkläder & Strumpor',
                'key_word' => '
                    Kalsonger, trosor, strumpor, strumpbyxor, Benvärmare, Boxershorts, Long johns, Strumpbyxor, Strumpbyxa, Strumpor, Trosor, 
                    Trosa, Underbyxor, Underbyxa Underklädsset, Underlinnen, Underlinne, Trunks,
                    Underwear, panties, stockings, tights, leg warmers, bloomers, boxer shorts, long johns, sports tops, sports tops, tights, tights, stockings, panties, Socks,
                    Panty, Pantyhose, Pantyhose Underwear set, Underliners, Underliners, Trunks
                ',
                'sub_key_word' => [
                    'Kalsonger' => 'Kalsonger, Kalsong, Underwear',
                    'Trosor' => 'Trosor, Trosa, Panties',
                    'Strumpor' => 'Strumpor, Strumpa, Socks, Sock',
                    'Strumpbyxor' => 'Strumpbyxor, Strumpbyxa, Stockings, Stocking',
                    'Benvärmare' => 'Benvärmare, Legwarmer, Leg warmer, Leg warmers, Legwarmers, Tights',
                    'Boxershorts' => 'Boxershorts, Boxers, Trunks',
                    'Långkalsonger' => 'Långkalsonger, Long-johns, Longjohns, Long johns',
                    'Underklädsset' => 'Underklädsset, Underwear set'
                ],
                'negative_key_words' => '
                    Kompressionsstrumpor
                ',
            ],
            [
                'name' => 'Toppar',
                'key_word' => '
                    topp, toppar, topar,tanktop, tanktopp,
                    top, tops
                ',
                'sub_key_word' => [
                    'sub Toppar' => 'Topp, Top, Toppar',
                    'Linnen' => 'Linne, Linnen, Tanktop, Tank tops'
                ],
                'negative_key_words' => '
                    Britax, Silver Cross,  Dockorna, Garderob, Ullmössa, mössa, vinterstövlar, jacka, gosedjur, 
                    dockhusmöbler, outfiten, Safari, Barnsängen, träningar, dockhus , bordslampa, Solglasögonen,
                    Barnbok, vinterstöveln, korten, trosorna, kalsongerna, boxerkalsongerna, fotoram, ryggsäck
                ',
            ],
            [
                'name' => 'Klänningar & kjolar',
                'key_word' => '
                    Klänning, Klänningar, Tunik, Tunikor, Tröjklänning, kjol, kjolar, tunika,
                    dress, dresses, skirt, skirts, tunic, tunics, Sweater dress, Sweaterdress                          
                ',
                'negative_key_words' => '
                    bloomers
                ',
                'sub_key_word' => [
                    'Klänningar' => 'Klänning, Klänningar, Dress, Dresses, Tyllklänningar, Tulle dresses,
                     vardagsklänning, vardagsklänningar, Casual dresses, casualdress',
                    'Tunikor' => 'Tunik, Tunikor, Tunic, Tunics',
                    'Tröjklänningar' => 'Tröjklänning, Sweater dress, Sweaterdress',
                    'Kjolar' => 'kjol, kjolar, skirt, skirts, jeanskjol, denimskirt, jeans skirt, korta kjolar, 
                        miniskirt, mini skirt, short skirt, maxikjolar, maxikjol maxidresses, maxidress, midikjolar, 
                        midikjol, midiskirts, midiskirt, tyllkjol, tyllkjolar, tulle skirts, tulleskirt, 
                        veckade kjolar, veckad kjol, pleated skirts, pleated skirt
                    '
                ]
            ],
            [
                'name' => 'Bodys & Bodysuits',
                'key_word' => '
                    Body, Sparkdräkt, Romper, Footsie, Onesie, Bodysuit 
                ',
                'sub_key_word' => [
                    'Bodys' => 'Body',
                    'Sparkdräkter' => 'Sparkdräkt, Romper, Bodysuit, Onesie, Footsie, Jumpsuit'
                ]
            ],
            [
                'name' => 'Pyjamas & Nattlinnen',
                'key_word' => '
                    Pyjamas, Nattlinne, Nattlinnen, Nattklänning, Nattklänningar,
                    Pajamas, Nightdress, Nightgown, Nightgowns 
                ',
                'sub_key_word' => [
                    'Pyjamas' => 'pyjamas, pyjamasar',
                    'Nattlinnen' => 'Nattlinne, Nattlinnen, Nightgown, Nightgowns',
                    'Nattklänningar' => 'Nattklänning, Nattklänningar, Nightdress'
                ]
            ],
            [
                'name' => 'Regnkläder',
                'key_word' => '
                    Regnhandskar, Regnhatt, Regnhattar, Regnoveraller, Regnoverall, Regnställ, 
                    Regnbyxor, Regnbyxa, Regnjacka 
                ',
                'sub_key_word' => [
                    'Regnjackor' => 'Regnjackor, regnjacka',
                    'Regnbyxor' => 'Regnbyxor, regnbyxa',
                    'Regnställ' => 'Regnställ',
                    'Regnhandskar' => 'Regnhandskar',
                    'Regnhattar' => 'Regnhattar, Regnhatt',
                    'sub Regnoveraller' => 'Regnoverall'
                ],
            ],
            [
                'name' => 'Fleecekläder',
                'key_word' => '
                    Fleecejacka, Fleece jacka, Vindfleece, Vind fleece, Fleecetröja, Fleece tröja, Fleecebyxa,
                    Fleece byxa, Fleece set, Fleeceoveraller, Fleecevantar,
                    Fleecejacket, Fleece jacket, Windfleece, Wind fleece, Fleecepants, Fleece pant
                ',
                'sub_key_word' => [
                    'Fleecejackor' => 'Fleecejacka, Fleecejackor, Fleece jacka, Fleecejacket, Fleece jacket, Vindfleece, Vind fleece, Wind fleece, Windfleece',
                    'Fleecetröjor' => 'Fleecetröja, Fleece tröja',
                    'Fleecebyxor' => 'Fleecebyxor, Fleecebyxa, Fleece byxor, Fleece byxor, Fleece byxa, Fleecepants, Fleece pants, Fleece pant, Fleece trousers',
                    'Fleeceoveraller' => 'Fleeceoverall, Fleeceoveraller',
                    'Fleecevantar' => 'Fleecevantar'
                ]
            ],
            [
                'name' => 'Jackor',
                'key_word' => '
                    Bomberjacka, Duffelkappa, Läderjacka, MC-jacka, Militärjacka, Regnjacka, Skaljacka, Skidjacka, Trenchcoat, Vadderad jacka, Vindjacka, Vinterjacka, 
                    Jeansjacka, Dunjacka, Löparjacka, Seglarjacka, Streetjacka, Vardagsjacka, Softshell jacka, Parkas
                ',
                'sub_key_word' => [
                    'Bomberjackor' => 'Bomberjacka',
                    'Duffelkappor' => 'Duffelkappa',
                    'Läderjackor' => 'Läderjacka',
                    'MC-jackor' => 'MC-jacka',
                    'Militärjackor' => 'Militärjacka',
                    'sub Regnjackor' => 'Regnjacka, Regnkappa, Regnrock',
                    'Skaljackor' => 'Skaljacka, Softshell jacka',
                    'Skidjackor' => 'Skidjacka',
                    'Trenchcoats' => 'Trenchcoat, Trench coat',
                    'Vindjackor' => 'Vindjacka',
                    'Vinterjackor' => 'Vinterjacka, Vadderad jacka, Dunjacka',
                    'Jeansjackor' => 'Jeansjacka',
                    'Löparjackor' => 'Löparjacka',
                    'Seglarjacka' => 'Seglarjacka',
                    'Streetjackor' => 'Streetjacka',
                    'Vardagsjackor' => 'Vardagsjacka',
                    'Parkas' => 'Parkas'
                ]
            ],
            [
                'name' => 'Overaller',
                'key_word' => '
                    Skaloveraller, Skaloverall, Skidbyxor, Skidbyxa, Thermobyxor, Termobyxor, Termobyxa,
                    Skidoveraller, Skidoverall, Vinteroverall, Vinteroveraller, Regnoveraller, Regnoverall
                ',
                'sub_key_word' => [
                    'Vinteroveraller' => 'vinteroverall, vinteroveraller',
                    'Regnoveraller' => 'regnoverall, regnoveraller',
                    'Skidoveraller' => 'skidoverall, skidoveraller',
                    'Skaloveraller' => 'skaloverall, skaloveraller',
                    'Skidbyxor' => 'skidbyxa, skidbyxor',
                    'Thermobyxor' => 'thermobyxor, thermobyxa, termobyxor, termobyxa'
                ]
            ],
            [
                'name' => 'Mössor & Vantar',
                'key_word' => '
                    Balaclavas, Baclava, Handskar, Handske, Vantar, Vante, Hattar, Hatt, Kepsar, Keps,
                    Mössor, Mössa, Huvudbonad, Solhatt, Halsdukar, halsduk
                ',
                'sub_key_word' => [
                    'Balaclavas' => 'Balaclavas, Baclava',
                    'Vantar & Handskar' => 'Vantar, Vante, Mittens, Mitten, Handskar, Handske, Gloves, Glove',
                    'Hattar' => 'Hattar, Hatt, Hats, Hat',
                    'Kepsar' => 'Kepsar, Keps, Caps, Cap',
                    'Mössor' => 'Mössor, Mössa, Huvudbonad',
                    'Halsdukar' => 'Halsduk, halsdukar'
                ]
            ],
            [
                'name' => 'Träningskläder',
                'key_word' => '
                    Träningsjacka, Träningsbyxa, Träningstopp, Joggingbyxa, Löparbyxor, 
                    Löparjacka, Löparkläder, Träningskläder, Sporttoppar, Sporttopp, 
                    Dansdräkt, Byxkjol, Tennis T-shirt,
                    Sweatpants, Running, Sweatwear, Sports, Sportsbra, Sportpants, Dancesuit
                ',
                'sub_key_word' => [
                    'Träningsjackor' => 'Träningsjacka',
                    'sub Träningsbyxor' => 'Träningsbyxa, Träningsbyxor, Sportpants',
                    'Löparbyxor' => 'Löparbyxor, Löparbyxa, Joggingbyxa, Joggingbyxor, Runningpants, Running pants, Jogging pants, Joggingpants',
                    'Träningsshorts' => 'Träningsshorts, Gymshorts, Gym shorts, Exerciseshorts, Exercise shorts',
                    'Träningstoppar' => 'Träningstopp, Gym topp, Gymtopp',
                    'sub Löparjackor' => 'Löparjacka, Runnin jacket, Jogging jacket',
                    'Löparkläder' => 'Löparbyxor, Löparbyxa, Joggingbyxa, Joggingbyxor, Löparjacka, Runnin jacket, Jogging jacket, Running clothes',
                    'Sporttoppar' => 'Sporttopp, Sport topp, Sportstop, Sportsbra, Sports bra',
                    'sub Flytvästar' => 'Lifevest, Life vest, Lifejacket, Life jacket, Flytväst, Livväst',
                    'Byxkjolar' => 'Byxkjol, Pantsuit, Pant suit',
                    'Träningströjor' => 'Sweatwear, Sweat-wear, Sweat wear, Exercise sweater, Exercisesweater',
                    'Tränings T-shirts' => 'Sports T-shirt, Sports Tshirt, Tränings T-shirt, Träningstshirt, Gymtshirt, Gym Tshirt, Gym T-shirt'
                ],
                'negative_key_words' => '
                    träpussel, Bok, bilderbok
                ',
            ],
            [
                'name' => 'Accessoarer',
                'key_word' => '
                    Barnklockor, Barnklocka, Paraplyer, Paraply, Plånböcker, Plånbok, 
                    Scarfs, Scarf, Drybibs, Drybib, Smycken, Smycke, Solglasögon, Bandanas, 
                    Bandana, Diadem, Nyckelringslampa, Pannband, Hårband, Nyckelring, Skärp, 
                    Ryggsäck, ryggsäckar, Flytväst, Räddningsväst,
                    Children\'s Watches, Children\'s Watch, Umbrellas, Umbrella, Wallets, Wallet, Scarfs,
                    Drybibs, Drybib, Jewelry, Sunglasses, Bandanas, Bandana, Diadem, 
                    Keychain Lamp, Headband, Hairband, Keychain, Belt, Belts
                ',
                'sub_key_word' => [
                    'Barnklockor' => 'Barnklockor, Barnklocka, Children\'s Watches, Children\'s Watch',
                    'Paraplyer' => 'Paraplyer, Paraply, Umbrellas, Umbrella',
                    'Plånböcker' => 'Plånböcker, Plånbok, Wallets, Wallet',
                    'Drybibs' => 'Drybibs, Drybib, Scarf, Scarfs',
                    'Smycken' => 'Smycken, Smycke, Jewelry',
                    'Solglasögon' => 'Solglasögon, Sunglasses',
                    'Bandanas' => 'Bandanas, Bandana',
                    'Diadem' => 'Diadem',
                    'Hårband' => 'Pannband, Hårband, Headband, Hairband',
                    'Nyckelringslampa' => 'Nyckelsingslampa, Keychain Lamp',
                    'Nyckelring' => 'Nyckelring, Nyckelringar, Keychain, Keychains',
                    'Skärp' => 'Skärp, Belt, Belts',
                    'Flytvästar' => 'Flytväst, Räddningsväst',
                    'Ryggsäckar' => 'ryggsäck, ryggsäckar'
                ],
            ],
        ]
    ];

    private $configurationsSize = [
        'SHOPPA EFTER STORLEK' => [
            '0-1 ÅR (50-80 CL)' => [
                'sizes' => '50, 56, 62, 68, 74, 80',
            ],
            '1-8 ÅR (86-128 CL)' => [
                'sizes' => '86, 92, 98, 104, 110, 116, 122, 128',
            ],
            '9-14 ÅR (134-164 CL)' => [
                'sizes' => '134, 140, 146, 152, 158, 164, 170',
            ],
        ]
    ];


    public function load(ObjectManager $manager)
    {
        $this->reUpdateFiles();
        $this->setManager($manager);

        $main = $this->createCategoryWithConf(
            'KLÄDER',
            'barn, Jr, Junior, Kids, Boys, Girls, Shirtstore, eStore, ungdom, Baby',
            'main',
            'barnvagnar, Leksaker, Skor, Maskerad, Kostym, Barndräkt, Halloween, Utklädnad, Sagodräkt, maskeradkläder');
        $configurations = $this->configurations;
        $this->processConfiguration($configurations, $main);
        $this->processSizeCategories($this->configurationsSize, $main);

        $this->afterLoad();
    }
}