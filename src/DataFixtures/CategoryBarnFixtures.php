<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\CategoryConfigurations;
use App\Entity\CategoryRelations;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryBarnFixtures extends Fixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $configurations = [
            [
                'name' => 'T-shirts',
                'key_word' => '
                    t-shirt, kortärmad, tee, shortsleve, piké, pike, långärmad t-shirt,
                    t-shirt, short-sleeved, tee, shortsleve, piké, girl, long-sleeved t-shirt
                ',
                'sub_key_word' => [
                    'subT-shirts' => 'T-shirt, Kortärmad, Tee, Shortsleve',
                    'Pikéer' => 'Piké, Rugbytröja, Rugby tröja',
                    'Långärmade t-shirts' => 'Långärmade t-shirts'
                ]
            ],
            [
                'name' => 'Toppar & linnen',
                'key_word' => '
                    topp, toppar, top, topar, linne, linnen, tanktop, tanktopp,
                    top, tops, top, tops, linen, linen, tank top, tank top
                ',
                'sub_key_word' => [
                    'Toppar' => 'Topp, Top, Toppar',
                    'Linnen' => 'Linne, Linnen, Tanktop, Tank tops'
                ],
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
                    'Zipjackor' => 'Zipjacka, Zipjackor, Zip jacka, Zip jackor, Zipjacket, Zip jacket, Zip-jacket, 
                    Sweatshirtjacket'
                ]
            ],
            [
                'name' => 'Klänningar & kjolar',
                'key_word' => '
                    klänning, klänningar, kjol, kjolar,
                    dress, dresses, skirt, skirts
                ',
                'sub_key_word' => [
                    'Klänningar' => 'Klänning, Klänningar, Dress, Dresses',
                    'Kjolar' => 'Kjol, Kjolar, Skirt, Skirts'
                ]
            ],
            [
                'name' => 'Skjortor',
                'key_word' => '
                    skjorta, skjortor, blus, blusar,
                    shirt, shirts, blouse, blouse
                ',
                'sub_key_word' => [
                    'subSkjortor' => 'Skjorta, Skjortor, Shirt, Shirts',
                    'Blusar' => 'Blus, Blusar, Blouse, Blouses'
                ]
            ],
            [
                'name' => 'Ytterkläder',
                'key_word' => '
                    jacka, jackor, fleece, flis, Balaclava, Bomberjackor, Capes, Duffelkappor, Dunjackor, Öronmuffar och vant set, Fleece set, Fleece tröjor, Fleecetröja, Fleece tröja, Fleecebyxa, Fleece byxa, Fleece byxor, Fleecetröja med huva, Fleecebyxor, Fleecejackor, Fleeceoveraller, Fleecetröjor, Fleecevanter, Fuskpäls och shearling, Gilet, Jeansjackor Kavajer och blazers, Leather gloves, Läderjackor, MC-jackor, Militärjackor, Mössa med öronlappar, Mössa, halsduk och vantar, Mössor, Parkas, Regnhandskar, Regnhatt, Regnjackor, Regnoveraller, Regnställ, Skaljackor, Regnjacka, Skaloveraller, Skid och thermobyxor, Skidhandskar och vantar, Skidjackor, Skidjacka, Skidoveraller, Skidoverall, Solhatt, Vår och höst jackor, Stickade halsdukar, Stickat halsduk, Trenchcoat, Träningsjackor, Ullvantar, Vadderade jackor, Vindjackor, Vinterjackor, Vinteroveraller, Västar, Jeansjacka, Jeansjackor, Längskidjacka, Löparjacka, Utomhus Jacka, Seglarjacka, Streetjacka, Vardagsjacka, Softshell, Soft-Shell,
                     jacket, jackets, fleece, fleece, balaclava, bomber jackets, capes, duffel jackets, down jackets, earmuffs and gloves set, fleece set, fleece sweaters, fleece sweater, fleece sweater, fleece trousers, fleece pants, fleece pants, fleece sweatshirt with hood, Fleece overalls, Fleece sweatshirts, Fleece vests, Faux fur and shearling, Gilet, Jeans jackets, Blazers and blazers, Leather gloves, Leather jackets, MC jackets, Military jackets, Ear wrap cap, Hat, scarf and mittens, Hats, Parkas, Rain gloves, Rain gloves, Rain gloves Raincoat, Ski jackets, Rain jacket, Overalls, Ski and Thermo pants, Ski gloves and mittens, Ski jackets, Ski jacket, Ski overalls, Ski overall, Sun hat, Spring and fall jackets, Knitted scarves, Knitted scarf, Trench coat, Training jackets, Wadded jackets, Woolen jackets Winter overalls, Vests, Jeans jacket, Jeans jackets, Cross-country ski jacket, Runner jacket, Outdoor jacket, Outdoor jacket, Sail jacket, Street jacket, Everyday jacket, Down Jacket, Softsh ell, Soft-Shell, Raincoat
                ',
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
                    'Fuskpäls och shearling' => 'Fuskpäls, Shearling,  Faux fur and shearling',
                    'Gilet' => 'Gilet',
                    'Läderhandskar' => 'Läderhandskar, Leather gloves
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
                    'Skid & Thermobyxor' => 'Skid och thermobyxor, Skidbyxor, Skidbyxa, Thermobyxa, Thermobyxor, Ski and Thermopants, 
                    Ski and Thermo pants',
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
                'name' => 'Byxor & shorts',
                'key_word' => '
                    Byxor, Chinos, Jeans, Sweatpants, Leggings, Jeggings, jeansshorts, jeans shorts, byxor, byxa, Cargopants, Termobyxor, Termobyxa, Skalbyxor, Skalbyxa, Cargobyxor, 
                    Cargobyxa, Haremsbyxor, Harembyxa, Mjukisbyxor, Mjukisbyxa, Regnbyxor, Regnbyxa, Shell pants, Trekvartsbyxor, Trekvartsbyxa, Capribyxor, Capribyxa,
                    Trousers, Chinos, Jeans, Sweatpants, Leggings, Jeggings, jeans shorts, jeans shorts, trousers, trousers, Cargopants, Termobyxor, Termobyxa, Shorts, Skalbyxa, 
                    Cargobyxor, Cargo Pants, Harem Pants, Harem Pants, Soft Pants, Soft Pants, Rain Pants, Rain Pants, Shell Pants, Three Quarter Pants, Three Quarter Pants, 
                    Capri Pants, Capri Pants, Sweatpants, Sweatshorts
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
                'name' => 'Underkläder',
                'key_word' => '
                    Kalsonger, trosor, strumpor, strumpbyxor, Benvärmare, Bloomers, Boxershorts, Long johns, Strumpbyxor, Strumpbyxa, Strumpor, Trosor, 
Trosa, Underbyxor, Underbyxa Underklädsset, Underlinnen, Underlinne, Trunks,
                     Underwear, panties, stockings, tights, leg warmers, bloomers, boxer shorts, long johns, sports tops, sports tops, tights, tights, stockings, panties, Socks
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
                    'Sporttoppar' => 'Sporttopp, Sporttoppar, Sportstop',
                    'Underklädsset' => 'Underklädsset, Underwear set',
                    'Underlinnen' => 'Underlinne, Underlinnen, Underliners'
                ]
            ],
            [
                'name' => 'Träningskläder',
                'key_word' => '
                    Träningsjacka, Träningsbyxa, Träningstopp, Joggingbyxa, Löparbyxor, Löparjacka, Löparkläder, Träningskläder, Sporttoppar, Sporttopp, Flytväst, Räddningsväst, 
Dansdräkt, Byxkjol, Tennis T-shirt,
                     Sweatpants, Sweatpants, Sweatpants, Jogging Pants, Running Pants, Running Jacket, Running Clothes, Sweatwear, Sports Bra, Sportsbra, Lifevest, Sportpants, 
Dancesuit
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
                ]
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
                'name' => 'UV & Bad',
                'key_word' => '
                    UV-Dräkt, UV-byxor, UV-byxa, uvtröja, uv-tröja, uv-tröjor, uvtröjor, UV-set, Badbyxor, Badbyxa, Badshorts, Bikini, Swimsuit, Swim Suit, Swimpants, Swim Pants, 
Swim Diapers, Blöjbadbyxor, Blöjbadbyxa, UV-Baddräckt, UV Badshorts, UV-Badshorts, Sunsuits, Sunsuit, Badtröja, Bad tröja, Badrockar, Badrock,
                     UV Apparel, UV Pants, UV Pants, Sweatshirt, UV Sweater, UV Sweatshirts, Sweatshirts, UV Sets, Swimsuit, Swimwear, Swimwear, Bikini, Swimsuit, Swim Suit, Swimpants, Swim Pants,
Swim Diapers, Diaper Tights, Diaper Tights, UV Bathing Suits, UV Bathing Shorts, UV Bathing Shorts, Sunsuits, Sunsuit, Sweater, Bathing Sweater, Bathing Suits, Bathrobe
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
                'name' => 'Accessoarer',
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
        ];
        $main = $this->createCategoryWithConf('Barn', 'Barn,barn');

        foreach ($configurations as $configuration) {
            $subMain = $this->createCategoryWithConf(
                $configuration['name'], $configuration['key_word']
            );

            $this->createCategoryRelations($main, $subMain);

            $subKeyWords = $configuration['sub_key_word'];
            if (is_array($subKeyWords) && count($subKeyWords) > 0) {
                $subKeyWordsArray = array_unique($subKeyWords);
                foreach ($subKeyWordsArray as $key => $words) {
                    $words = preg_replace('/\s+/', '', $words);

                    $wordCategory = $this->createCategoryWithConf(
                        $key, $words
                    );

                    $this->createCategoryRelations($subMain, $wordCategory);
                }
            }

            $manager->flush();
        }

    }

    /**
     * @param string $categoryName
     * @param string $keyWords
     * @return Category
     */
    private function createCategoryWithConf(string $categoryName, string $keyWords): Category
    {
        $category = $this->checkExistCategory($categoryName);

        if (!$category instanceof Category) {
            $category = new Category();
            $category->setCategoryName($categoryName);
        }

        $category
            ->setCustomeCategory(true);

        $this->getManager()->persist($category);

        $categoryConfigurations = $category->getCategoryConfigurations();
        if (!$categoryConfigurations) {
            $categoryConfigurations = new CategoryConfigurations();
        }

        $categoryConfigurations
            ->setKeyWords($keyWords);

        $category->setCategoryConfigurations($categoryConfigurations);

        $this->getManager()->persist($categoryConfigurations);

        return $category;
    }

    /**
     * @param Category $m
     * @param Category $s
     * @return CategoryRelations
     */
    private function createCategoryRelations(Category $m, Category $s)
    {
        $categoryRelations = new CategoryRelations();
        $categoryRelations
            ->setMainCategory($m)
            ->setSubCategory($s);

        $this->getManager()->persist($categoryRelations);

        return $categoryRelations;
    }

    /**
     * @return ObjectManager
     */
    private function getManager()
    {
        return $this->manager;
    }

    /**
     * @return \Doctrine\Persistence\ObjectRepository|CategoryRepository
     */
    private function getCategoryRepository()
    {
        return $this->getManager()->getRepository(Category::class);
    }

    /**
     * @param string $categoryName
     * @return Category|object|null
     */
    private function checkExistCategory(string $categoryName)
    {
        return $this->getCategoryRepository()
            ->findOneBy(['categoryName' => $categoryName]);
    }
}
