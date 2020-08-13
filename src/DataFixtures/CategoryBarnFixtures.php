<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;

class CategoryBarnFixtures extends AbstractFixtures
{
    /**
     * @var array
     */
    private $configurations = [
        'KATEGORI' => [
            [
                'name' => 'Ytterkläder',
                'key_word' => '
                    jacka, jackor, fleece, flis, Balaclava, Bomberjackor, Capes,Duffelkappor, Dunjackor, Gilet, 
                    Kavajer, gloves, Läderjackor, MC-jackor,  Mössa, halsduk, vantar, Parkas, Regnhandskar, 
                    Regnhatt, thermobyxor, Skidhandska, kidjackor, Skidoveraller, Skidoverall, Solhatt, Trenchcoat,  
                    Vinterjackor, Vardagsjacka,  Streetjacka, Löparjacka,                    
                    jacket, jackets, fleece, fleece, balaclava, bomber jackets, capes,  gloves, Blazers, cap, 
                    Hat, scarf, mittens, Hats, Parkas, Raincoat, Overalls, Thermo, coat, Vests, Raincoat                     
                ',
                'negative_key_words' => 'mugghållare, transportväska, chassi, Thule, solhatt, sandal, pärlsammetfilt',
                'sub_key_word' => [
                    'Balaclava' => 'Balaclava, Balaclavor',
                    'Bomberjackor' => 'Bomberjacka, Bomberjackor, Bomberjacket, Bomber jacket',
                    'Capes' => 'Cape, Capes',
                    'Duffelkappor' => 'Duffeljacka, Duffeljackor, Duffel jacket',
                    'Fleece set' => 'Fleece set',
                    'Fleecetröjor' => 'Fleecetröja, Fleece tröja, Fleecetröjor, Fleece tröjor, Fleece sweaters, Fleece sweater, Fleece sweater',
                    'Fleecebyxor' => 'Fleecebyxa, Fleece byxa, Fleece byxor, Fleecebyxor,  Fleece trousers, Fleece pant, Fleece pants',
                    'Fleecejackor' => 'Fleecejackor, Fleecejacka, Fleece jacka, Fleece jackor',
                    'Fleeceoveraller' => 'Fleeceoveraller, Fleece overalls',
                    'Fleecevantar' => 'Fleecevantar, Fleece vantar',
                    'Fuskpäls och shearling' => 'Fuskpäls, Shearling,  Faux fur, shearling',
                    'Gilet' => 'Gilet',
                    'Läderhandskar' => 'Läderhandskar, Leather gloves,
                        Läderjackor, Läderjacka, Läder jacka, Läderjackor, Leather jacket, Leatherjacket, Leather jackets, Leatherjackets, MC-jacka, MC-jackor',
                    'Militärjackor' => 'Militärjacka, Militärjackor, Militär jacka, Militär jackor, Military jacket, Military jackets',
                    'Mössor' => 'Mössa med öronlappar, Mössa, Mössor, Ear wrap cap, Hat',
                    'Halsdukar' => 'Halsduk, Halsduk och vantar, Neck scarf, Neckscarf, Stickade halsdukar, Stickad halsduk',
                    'Vantar' => 'Vantar, Mitts, Mittens, Rain gloves, Handskar, Regnhandskar',
                    'Parkas' => 'Parkas',
                    'Regnhattar' => 'Regnhattar, Regnhatt, Rainhat, Rain hat',
                    'Regnjackor' => 'Regnjacka, Regnjackor, Raincoat, Rain coat, Raincoats, Rain coats',
                    'Regnoveraller' => 'Regnoveraller, Regnoverall, Regnställ, Rainoveralls, Rain overalls, Rain overall',
                    'Skaljackor' => 'Skaljacka, Skaljackor, Shell-jacket, Shell-jacket, Shell jacket, Shelljacket',
                    'Skaloveraller' => 'Skaloverall, Skaloveraller, Shell-overalls, Shell-overall, Shell overalls, Shelloveralls',
                    'Skid & Thermobyxor' => 'Skid och thermobyxor, Skidbyxor, Skidbyxa, Thermobyxa, Thermobyxor, Ski, Thermopants,
                    Ski, Thermo pants',
                    'Skidjackor' => 'Skidjacka, Skid jacka, Skijacket, Ski jacket',
                    'Skidoveraller' => 'Skidoveraller, Skid overaller, Ski overalls, Skioveralls, Ski overall, Ski overalls',
                    'Solhattar' => 'Solhatt, Sunhat, Sun hat',
                    'Trenchcoats' => 'Trenchcoat',
                    'Vadderade jackor' => 'Vadderad jacka, Padded jacket, Wadded jacket, Woolen jacket',
                    'Vindjackor' => 'Wind jacket, Windjacket, Vindjacka, Vind jacka',
                    'Vinterjackor' => 'Vinterjacka, Vinter jacka, Winter jacket, Winterjacket',
                    'Vinteroveraller' => 'Vinteroverall, Winter overall, Winteroverall',
                    'Västar' => 'Väst, Vest',
                    'Jeansjackor' => 'Jeansjacka, Jeans jacka, Jeans jacket',
                    'Dunjackor' => 'Dunjacka, Downjacket, Down jacket',
                    'Löparjackor' => 'Löparskor, Löpar skor, Running shoes, Running shoe, Joggingskor, Jogging skor, Joging shoes',
                    'Outdoor Jackor' => 'Outdoor jacka, Outdoorjacka',
                    'Seglarjacka' => 'Seglarjacka, Sail jacket, Sailjacket',
                    'Streetjackor' => 'Streatjacka, Streat jacka, Streatjacket, Streat jacket',
                    'Vardagsjackor' => 'Vardagsjacka, Everyday jacket',
                    'Softshell jackor' => 'Softschelljacka, Softschell jacka, Soft-shell jacket, Soft-shelljacket, Soft shell jacket'
                ]
            ],
            [
                'name' => 'T-shirts',
                'key_word' => '
                    t-shirt, kortärmad, tee, shortsleve, piké, pike, långärmad t-shirt, Rugbytröja, Rugby tröja,
                    t-shirt, short-sleeved, tee, shortsleeve, piké, long-sleeved t-shirt
                ',
                'negative_key_words' => 'dress',
                'sub_key_word' => [
                    'subT-shirts' => 'T-shirt, Kortärmad, Tee, Shortsleeve, short sleeve, short sleeved',
                    'Pikéer' => 'Piké, Rugbytröja, Rugby tröja',
                    'Långärmade t-shirts' => 'Långärmad t-shirt'
                ]
            ],
            [
                'name' => 'subToppar',
                'key_word' => '
                    topp, toppar, topar,tanktop, tanktopp, top, tops
                ',
                'sub_key_word' => [
                    'Toppar' => 'Topp, Top, Toppar',
                    'Linnen' => 'Linne, Linnen, Tanktop, Tank tops'
                ],
                'negative_key_words' => '
                    Britax, Silver Cross,  Dockorna, Garderob, Ullmössa, mössa, vinterstövlar, 
                    jacka, gosedjur, dockhusmöbler, outfiten, Safari, Barnsängen, träningar, dockhus, bordslampa, 
                    Solglasögonen, Barnbok, vinterstöveln, korten
                ',
            ],
            [
                'name' => 'Tröjor & koftor',
                'key_word' => '
                    tröja, tröjor, uvtröja, uv-tröja, uv-tröjor, uvtröjor, kofta, koftor, cardigan, cardigans, hoodie, hoodies, luvtröja, Zip-jacka,
                    sweatshirt, sweater, sweatshirts, uv shirt, uv shirts, Zipjacket, Zip jacket, Zip-jacket, Sweatshirtjacket
                ',
                'sub_key_word' => [
                    'Tröjor' => 'Tröja, Tröjor, Sweatshirt, Sweater, Sweatshirts',
                    'Koftor' => 'Kofta, Koftor',
                    'Cardigans' => 'Cardigan, Cardigans',
                    'Luvtröjor' => 'Luvtröja, Luvtröjor, Hoodie, Hoddies',
                    'Zipjackor' => 'Zipjacka, Zipjackor, Zip jacka, Zip jackor, Zipjacket, Zip jacket, Zip-jacket, Sweatshirtjacket'
                ],
                'negative_key_words' => '
                    väggkrok, Byxa, shorts, pants, skirt,  loftsäng
                ',
            ],
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
                ]
            ],
            [
                'name' => 'Skjortor & Blusar',
                'key_word' => '
                    skjorta, skjortor, blus, blusar,
                    shirt, shirts, blouse, blouse
                ',
                'sub_key_word' => [
                    'Skjortor' => 'Skjorta, Skjortor, Shirt, Shirts',
                    'Blusar' => 'Blus, Blusar, Blouse, Blouses'
                ],
                'negative_key_words' => '
                    Dress, pant, pants, shorts, Skirt, tee, väggkrok, tröja, Tunika, Topp
                ',
            ],
            [
                'name' => 'Byxor & shorts',
                'key_word' => '
                    Byxor, Chinos, Jeans, Sweatpants, Leggings, Jeggings, shorts, byxa, Cargopants, Termobyxor, 
                    Termobyxa, Skalbyxor, Skalbyxa, Cargobyxor, Cargobyxa, Haremsbyxor, Harembyxa, Mjukisbyxor, 
                    Mjukisbyxa, Regnbyxor, Regnbyxa, Trekvartsbyxor, Trekvartsbyxa, Capribyxor, Capribyxa, 
                    Träningsbyxor, Träningsbyxa,
                    
                    Trousers, Sweatpants, Cargopants, Shorts, Skalbyxa, 
                    Cargobyxor, Pants, Sweatpants, Sweatshorts                          
                ',
                'negative_key_words' => '
                    Emmaljunga, babysitter, muslin, Linne, Linnes, strumpor, Jackan, kofta, t-shirt, 
                    tröja, väggkrok, barnvagnar
                ',
                'sub_key_word' => [
                    'Chinos' => 'Chinos',
                    'Jeans' => 'Jeans',
                    'Mjukisbyxor' => 'Mjukisbyxa, Mjukisbyxor, Sweatpants, Sweatshorts',
                    'Leggings' => 'Leggings',
                    'Jeggings' => 'Jeggings',
                    'Jeansshorts' => 'Jeansshorts, Jeans shorts',
                    'Cargobyxor' => 'Cargopants, Cargobyxor',
                    'Termobyxor' => 'Thermobyxa, Thermobyxor, Thermopants, Thermopants',
                    'Skalbyxor' => 'Skalbyxa, Skalbyxor, Shell-pants, Shell pants, Shellpants',
                    'Haremsbyxor' => 'Harembyxa, Harembyxor, Haremtrousers, Harem trousers',
                    'Regnbyxor' => 'Regnbyxa, Regnbyxor, Rainpants, Rain pants',
                    'Trekvartsbyxor' => 'Trekvartsbyxor, Three Quarter Pants',
                    'Capribyxor' => 'Capribyxa, Capribyxor, Capripants, Capri pants',
                    'Träningsbyxor' => 'Träningsbyxa, Träningsbyxor, Workoutpants, Workout pants, Training pants, Exercisepants, Exercise pants'
                ]
            ],
            [
                'name' => 'Barn Underkläder',
                'key_word' => '
                    Kalsonger, trosor, strumpor, strumpbyxor, Benvärmare, Bloomers, Boxershorts, Long johns, Strumpbyxor, Strumpbyxa, Strumpor, Trosor, 
                    Trosa, Underbyxor, Underbyxa Underklädsset, Underlinnen, Underlinne, Trunks,
                    Underwear, panties, stockings, tights, leg warmers, bloomers, boxer shorts, long johns, sports tops, sports tops, tights, tights, stockings, panties, Socks
                    Panty, Pantyhose, Pantyhose Underwear set, Underliners, Underliners, Trunks
                ',
                'negative_key_words' => 'Kompressionsstrumpor',
                'sub_key_word' => [
                    'Kalsonger' => 'Kalsonger, Kalsong, Underwear',
                    'Trosor' => 'Trosor, Trosa, Panties',
                    'Strumpor' => 'Strumpor, Strumpa, Socks, Sock',
                    'Strumpbyxor' => 'Strumpbyxor, Strumpbyxa, Stockings, Stocking',
                    'Benvärmare' => 'Benvärmare, Legwarmer, Leg warmer, Leg warmers, Legwarmers, Tights',
                    'Boxershorts' => 'Boxershorts, Boxers, Trunks',
                    'Långkalsonger' => 'Långkalsonger, Long-johns, Longjohns, Long johns',
                    'Sporttoppar' => 'Sporttopp, Sporttoppar, Sportstop',
                    'Underklädsset' => 'Underklädsset, Underwear set',
                    'Underlinnen' => 'Underlinne, Underlinnen, Underliners'
                ]
            ],
            [
                'name' => 'Träningskläder',
                'key_word' => '
                    Träningsjacka, Träningsbyxa, Träningstopp, Joggingbyxa, Löparbyxor, Löparjacka, Löparkläder, 
                    Träningskläder, Sporttoppar, Sporttopp, Flytväst, Räddningsväst, 
                    Dansdräkt, Byxkjol, Tennis T-shirt,
                    Sweatpants, Running, Sweatwear, Sports, Sportsbra, Lifevest, Sportpants, Dancesuit
                ',
                'sub_key_word' => [
                    'Träningsjackor' => 'Träningsjacka',
                    'Träningsbyxor' => 'Träningsbyxa, Träningsbyxor, Sportpants',
                    'Löparbyxor' => 'Löparbyxor, Löparbyxa, Joggingbyxa, Joggingbyxor, Runningpants, Running pants, Jogging pants, Joggingpants',
                    'Träningsshorts' => 'Träningsshorts, Gymshorts, Gym shorts, Exerciseshorts, Exercise shorts',
                    'Träningstoppar' => 'Träningstopp, Gym topp, Gymtopp',
                    'Löparjackor' => 'Löparjacka, Runnin jacket, Jogging jacket',
                    'Löparkläder' => 'Löparbyxor, Löparbyxa, Joggingbyxa, Joggingbyxor, Löparjacka, Runnin jacket, Jogging jacket, Running clothes',
                    'Sporttoppar' => 'Sporttopp, Sport topp, Sportstop, Sportsbra, Sports bra',
                    'Flytvästar' => 'Lifevest, Life vest, Lifejacket, Life jacket, Flytväst, Livväst',
                    'Byxkjolar' => 'Byxkjol, Pantsuit, Pant suit',
                    'Träningströjor' => 'Sweatwear, Sweat-wear, Sweat wear, Exercise sweater, Exercisesweater',
                    'Tränings T-shirts' => 'Sports T-shirt, Sports Tshirt, Tränings T-shirt, Träningstshirt, Gymtshirt, Gym Tshirt, Gym T-shirt'
                ]
            ],
            [
                'name' => 'Kavajer & västar',
                'key_word' => '
                    Kavajer, Kavaj, Väst, Västar,
                     Jackets, Jacket, Vest, Vests
                ',
                'sub_key_word' => [
                    'Kavajer' => 'Blazers, Blazer, Kavaj, Kavajer',
                    'Västar' => 'Vest, Vests, Väst'
                ],
                'negative_key_words' => 'träpussel, Bok, bilderbok'
            ],
            [
                'name' => 'Jumpsuits',
                'key_word' => '
                    Jumpsuit, Jumpsuit, Sparkdräkt, Bodysuit,
                     Jumpsuit, Jumpsuit, Spark suit, Bodysuit
                ',
                'sub_key_word' => [
                    'subJumpsuits' => 'Jumpsuit, Jumpsuits, overall, overalls',
                    'Sparkdräkter' => 'Sparkdräkt, Sparkdräkter, Spark suit',
                    'Bodysuits' => 'Bodysuit, Bodysuits'
                ]
            ],
            [
                'name' => 'Barn UV & Bad',
                'key_word' => '
                Baddräkt, Baddräkter, UV-Dräkt, UV-byxor, UV-byxa, uvtröja, uv-tröja, uv-tröjor, uvtröjor, UV-set, Badbyxor, Badbyxa, Badshorts, Bikini, Swimsuit, Swim Suit, Swimpants, Swim Pants, Swim Diapers, Blöjbadbyxor, Blöjbadbyxa, UV-Baddräkt, UV Badshorts, UV-Badshorts, Sunsuits, Sunsuit, Badtröja, Bad tröja, Badrockar, Badrock, Morgonrock,
                UV Apparel, UV Pants, UV Pants, Sweatshirt, UV Sweater, UV Sweatshirts, Sweatshirts, UV Sets, Swimsuit, Swimwear, Swimwear, Bikini, Swimsuit, Swim Suit, Swimpants, Swim Pants, Swim Diapers, Diaper Tights, Diaper Tights, UV Bathing Suits, UV Bathing Shorts, UV Bathing Shorts, Sunsuits, Sunsuit, Sweater, Bathing Sweater, Bathing Suits, Bathrobe
            ',
                'sub_key_word' => [
                    'Badshorts' => 'Badbyxor, Badbyxa, Badshorts, Swimpants, Swim Pants, UV Badshorts, UV-Badshorts',
                    'Baddräkt' => 'UV-Baddräkt, Sunsuits, Sunsuit, Baddräkt, Baddräkter, swimsuit, swimsuites, swim suit, swim suites',
                    'Bikini' => 'Bikini, Bikinis',
                    'Blöjbad' => 'Swim Diapers, Blöjbadbyxor, Blöjbadbyxa, Blöjbadtrosa, Blöjbadtrosor',
                    'UV-Dräkt' => 'UV-Dräkt, UV-Dräkter, uv dräkter, uv dräkt, UV-set',
                    'UV-byxor' => 'UV-byxor, UV-byxa, uv byxa, uv byxor, UV-set',
                    'UV-tröja' => 'uvtröja, uv-tröja, uv-tröjor, uvtröjor, UV-set, Badtröja, Bad tröja',
                    'Badrockar' => 'Badrockar, Badrock, Bathrobe, Morgonrock'
                ]
            ],
            [
                'name' => 'Sov & mysplagg',
                'key_word' => '
                    Pyjamas, Morgonrock, Badrockar, Badrock, Nattlinne, Nattklänning,
                     Pajamas, Bathrobe, Bathrobes, Nightdress
                ',
                'sub_key_word' => [
                    'Pyjamas' => 'pyjamas, pyjamasar',
                    'Nattlinnen' => 'Nattlinne, Nattlinnen, Nightgown, Nightgowns',
                    'Nattklänningar' => 'Nattklänning, Nattklänningar, Nightdress'
                ]
            ],
            [
                'name' => 'Barn Accessoarer',
                'key_word' => '
                    Balaclavas, Baclava, Barnklockor, Barnklocka, Halsdukar, Halsduk, Handskar, Handske, Vantar, Vante, Hattar, Hatt, Kepsar, Keps, Mössor, Mössa, 
                    Paraplyer, Paraply, Plånböcker, Plånbok, Scarfs, Scarf, Drybibs, Drybib, Smycken, Smycke, Solglasögon, Bandanas, Bandana, Diadem, Nyckelsingslampa, Pannband, 
                    Hårband, Nyckelring, Skärp,
                    Balaclavas, Baclava, Children\'s Watches, Children\'s Watch, Scarves, Scarf, Gloves, Glove, Mittens, Mitten, Hats, Hat, Caps, Cap,
                    Umbrellas, Umbrella, Wallets, Wallet, Scarfs, Drybibs, Drybib, Jewelry, Sunglasses, Bandanas, Bandana, Diadem, Keychain Lamp, 
                    Headband, Hairband, Keychain, Belt, Belts
                ',
                'sub_key_word' => [
                    'Balaclavas' => 'Balaclavas, Baclava',
                    'Barnklockor' => 'Barnklockor, Barnklocka, Children\'s Watches, Children\'s Watch',
                    'Halsdukar' => 'Halsdukar, Halsduk',
                    'Handskar' => 'Handskar, Handske, Gloves, Glove',
                    'Vantar' => 'Vantar, Vante, Mittens, Mitten',
                    'Hattar' => 'Hattar, Hatt, Hats, Hat',
                    'Kepsar' => 'Kepsar, Keps, Caps, Cap',
                    'Mössor' => 'Mössor, Mössa',
                    'Paraplyer' => 'Paraplyer, Paraply, Umbrellas, Umbrella',
                    'Plånböcker' => 'Plånböcker, Plånbok, Wallets, Wallet',
                    'Scarfs' => 'Scarfs, Scarf, Drybibs, Drybib',
                    'Smycken' => 'Smycken, Smycke, Jewelry',
                    'Solglasögon' => 'Solglasögon, Sunglasses',
                    'Bandanas' => 'Bandanas, Bandana',
                    'Diadem' => 'Diadem',
                    'Hårband' => 'Pannband, Hårband, Headband, Hairband',
                    'Nyckelringslampa' => 'Nyckelsingslampa, Keychain Lamp',
                    'Nyckelring' => 'Nyckelring, Nyckelringar, Keychain, Keychains',
                    'Skärp' => 'Skärp, Belt, Belts'
                ]
            ],
        ]
    ];

    private $configurationsSize = [
        'KLÄDER 92-128 CL (1-8 ÅR)' => [
            '(1-8 ÅR)Visa alla' => [
                'size' => '92, 98, 104, 110, 116, 122, 128',
                'positive_key_words' => 'girl',
            ],
            '(1-8 ÅR)Flicka' => [
                'sizes' => '92, 98, 104, 110, 116, 122, 128',
                'positive_key_words' => 'girl',
            ],
            '(1-8 ÅR)Pojke' => [
                'sizes' => '92, 98, 104, 110, 116, 122, 128',
                'positive_key_words' => 'boy',
            ]
        ],
        'KLÄDER 134-164 CL (9-14 ÅR)' => [
            '(9-14 ÅR)Visa alla' => [
                'size' => '134, 140, 146, 152, 158, 164',
                'positive_key_words' => 'girl',
            ],
            '(9-14 ÅR)Flicka' => [
                'sizes' => '134, 140, 146, 152, 158, 164',
                'positive_key_words' => 'girl',
            ],
            '(9-14 ÅR)Pojke' => [
                'sizes' => '134, 140, 146, 152, 158, 164',
                'positive_key_words' => 'boy',
            ]
        ]
    ];

    public function load(ObjectManager $manager)
    {
        $this->reUpdateFiles();
        $this->setManager($manager);

        $main = $this->createCategoryWithConf(
            'Barn',
            'barn, Jr, Junior, Kids',
            'main',
            'barnvagnar, Leksaker, Skor, Baby, Maskerad, Kostym, Barndräkt, Halloween, Utklädnad, Sagodräkt');
        $configurations = $this->configurations;
        $this->processConfiguration($configurations, $main);
        $this->processSizeCategories($this->configurationsSize, $main);
        $this->afterLoad();
    }
}
