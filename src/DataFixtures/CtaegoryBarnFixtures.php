<?php

namespace App\DataFixtures;

use App\Entity\CategoryConfigurations;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CtaegoryBarnFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $configurations = [
            [
                'name' => 'T-shirts',
                'key_word' => '
                    t-shirt, kortärmad, tee, shortsleve, piké, pike, långärmad t-shirt,
                    t-shirt, short-sleeved, tee, shortsleve, piké, girl, long-sleeved t-shirt
                ',
                'sub_key_word' => 'T-shirts, Pikéer, Långärmade t-shirts'
            ],
            [
                'name' => 'Toppar & linnen',
                'key_word' => '
                    topp, toppar, top, topar, linne, linnen, tanktop, tanktopp,
                    top, tops, top, tops, linen, linen, tank top, tank top
                ',
                'sub_key_word' => 'Toppar, Linnen'
            ],
            [
                'name' => 'Tröjor & koftor',
                'key_word' => '
                    tröja, tröjor, uvtröja, uv-tröja, uv-tröjor, uvtröjor, kofta, koftor, cardigan, cardigans, hoodie, hoodies, luvtröja, Zip-jacka,
                    sweatshirt, sweater, sweatshirts, uv shirt, uv shirts, Zipjacket, Zip jacket, Zip-jacket, Sweatshirtjacket
                ',
                'sub_key_word' => 'Tröjor, Koftor, Cardiagans, Luvtröjor, Uv-tröjor, Zipjackor'
            ],
            [
                'name' => 'Klänningar & kjolar',
                'key_word' => '
                    klänning, klänningar, kjol, kjolar,
                    dress, dresses, skirt, skirts
                ',
                'sub_key_word' => 'Klänningar, Kjolar'
            ],
            [
                'name' => 'Skjortor',
                'key_word' => '
                    skjorta, skjortor, blus, blusar,
                    shirt, shirts, blouse, blouse
                ',
                'sub_key_word' => 'Skjortor, Blusar'
            ],
            [
                'name' => 'Ytterkläder',
                'key_word' => '
                    jacka, jackor, fleece, flis, Balaclava, Bomberjackor, Capes, Duffelkappor, Dunjackor, Öronmuffar och vant set, Fleece set, Fleece tröjor, Fleecetröja, Fleece tröja, Fleecebyxa, Fleece byxa, Fleece byxor, Fleecetröja med huva, Fleecebyxor, Fleecejackor, Fleeceoveraller, Fleecetröjor, Fleecevanter, Fuskpäls och shearling, Gilet, Jeansjackor Kavajer och blazers, Leather gloves, Läderjackor, MC-jackor, Militärjackor, Mössa med öronlappar, Mössa, halsduk och vantar, Mössor, Parkas, Regnhandskar, Regnhatt, Regnjackor, Regnoveraller, Regnställ, Skaljackor, Regnjacka, Skaloveraller, Skid och thermobyxor, Skidhandskar och vantar, Skidjackor, Skidjacka, Skidoveraller, Skidoverall, Solhatt, Vår och höst jackor, Stickade halsdukar, Stickat halsduk, Trenchcoat, Träningsjackor, Ullvantar, Vadderade jackor, Vindjackor, Vinterjackor, Vinteroveraller, Västar, Jeansjacka, Jeansjackor, Längskidjacka, Löparjacka, Utomhus Jacka, Seglarjacka, Streetjacka, Vardagsjacka, Softshell, Soft-Shell,
                     jacket, jackets, fleece, fleece, balaclava, bomber jackets, capes, duffel jackets, down jackets, earmuffs and gloves set, fleece set, fleece sweaters, fleece sweater, fleece sweater, fleece trousers, fleece pants, fleece pants, fleece sweatshirt with hood, Fleece overalls, Fleece sweatshirts, Fleece vests, Faux fur and shearling, Gilet, Jeans jackets, Blazers and blazers, Leather gloves, Leather jackets, MC jackets, Military jackets, Ear wrap cap, Hat, scarf and mittens, Hats, Parkas, Rain gloves, Rain gloves, Rain gloves Raincoat, Ski jackets, Rain jacket, Overalls, Ski and Thermo pants, Ski gloves and mittens, Ski jackets, Ski jacket, Ski overalls, Ski overall, Sun hat, Spring and fall jackets, Knitted scarves, Knitted scarf, Trench coat, Training jackets, Wadded jackets, Woolen jackets Winter overalls, Vests, Jeans jacket, Jeans jackets, Cross-country ski jacket, Runner jacket, Outdoor jacket, Outdoor jacket, Sail jacket, Street jacket, Everyday jacket, Down Jacket, Softsh ell, Soft-Shell, Raincoat
                ',
                'sub_key_word' => 'Jackor, Balaclava, Bomberjackor, Capes, Duffelkappor, Fleece set, Fleecetröjor, Fleecebyxor, Fleecejackor, Fleeceoveraller, Fleecevantar, Fuskpäls och shearling, Gilet, Läderhandskar, Läderjackor, MC-jackor, Militärjackor, Mössor, Halsdukar, Vantar, Parkas, Regnhandskar, Regnhattar, Regnjackor, Regnoveraller, Regnställ, Skaljackor, Skaloveraller, Skid och thermobyxor, Skidjackor, Skidoveraller, Solhattar, Trenchcoats, Vadderade jackor, Vindjackor, Vinterjackor, Vinteroveraller, Västar, Jeansjackor, Dunjackor, Löparjackor, Outdoor Jackor, Seglarjacka, Streetjackor, Vardagsjackor, Softshell jackor'
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
                'sub_key_word' => 'Chinos, Jeans, Sweatpants, Leggings, Jeggings, jeansshorts, 
                    Cargopants, Termobyxor, Skalbyxor, Cargobyxor, Haremsbyxor, 
                    Mjukisbyxor, Regnbyxor, Trekvartsbyxor, Capribyxor, Träningsbyxor
                '
            ],
            [
                'name' => 'Underkläder',
                'key_word' => '
                    Kalsonger, trosor, strumpor, strumpbyxor, Benvärmare, Bloomers, Boxershorts, Long johns, Strumpbyxor, Strumpbyxa, Strumpor, Trosor, 
Trosa, Underbyxor, Underbyxa Underklädsset, Underlinnen, Underlinne, Trunks,
                     Underwear, panties, stockings, tights, leg warmers, bloomers, boxer shorts, long johns, sports tops, sports tops, tights, tights, stockings, panties, Socks
Panty, Pantyhose, Pantyhose Underwear set, Underliners, Underliners, Trunks
                ',
                'sub_key_word' => 'Kalsonger, Trosor, Strumpor, Strumpbyxor, Benvärmare, Boxershorts, 
Longkalsonger, Sporttoppar, Underklädsset, Underlinnen'
            ],
            [
                'name' => 'Träningskläder',
                'key_word' => '
                    Träningsjacka, Träningsbyxa, Träningstopp, Joggingbyxa, Löparbyxor, Löparjacka, Löparkläder, Träningskläder, Sporttoppar, Sporttopp, Flytväst, Räddningsväst, 
Dansdräkt, Byxkjol, Tennis T-shirt,
                     Sweatpants, Sweatpants, Sweatpants, Jogging Pants, Running Pants, Running Jacket, Running Clothes, Sweatwear, Sports Bra, Sportsbra, Lifevest, Sportpants, 
Dancesuit
                ',
                'sub_key_word' => 'Träningsjackor, Träningsbyxor, Träningsshorts, Träningstoppar, Joggingbyxor, Löparbyxor, 
Löparjacka, Löparkläder, Sporttoppar, Flytvästar, Byxkjolar, Träningströjor, Tränings T-shirts'
            ],
            [
                'name' => 'Kavajer & västar',
                'key_word' => '
                    Kavajer, Kavaj, Väst, Västar,
                     Jackets, Jacket, Vest, Vests
                ',
                'sub_key_word' => 'Kavajer, Västar'
            ],
            [
                'name' => 'Jumpsuits',
                'key_word' => '
                    Jumpsuit, Jumpsuit, Sparkdräkt, Bodysuit,
                     Jumpsuit, Jumpsuit, Spark suit, Bodysuit
                ',
                'sub_key_word' => 'Jumpsuits, Sparkdräkter, Bodysuits'
            ],
            [
                'name' => 'UV & Bad',
                'key_word' => '
                    UV-Dräkt, UV-byxor, UV-byxa, uvtröja, uv-tröja, uv-tröjor, uvtröjor, UV-set, Badbyxor, Badbyxa, Badshorts, Bikini, Swimsuit, Swim Suit, Swimpants, Swim Pants, 
Swim Diapers, Blöjbadbyxor, Blöjbadbyxa, UV-Baddräckt, UV Badshorts, UV-Badshorts, Sunsuits, Sunsuit, Badtröja, Bad tröja, Badrockar, Badrock,
                     UV Apparel, UV Pants, UV Pants, Sweatshirt, UV Sweater, UV Sweatshirts, Sweatshirts, UV Sets, Swimsuit, Swimwear, Swimwear, Bikini, Swimsuit, Swim Suit, Swimpants, Swim Pants,
Swim Diapers, Diaper Tights, Diaper Tights, UV Bathing Suits, UV Bathing Shorts, UV Bathing Shorts, Sunsuits, Sunsuit, Sweater, Bathing Sweater, Bathing Suits, Bathrobe
                ',
                'sub_key_word' => 'Badshorts, Baddräkt, Bikini, UV-Dräkt, UV-byxor, UV-tröja'
            ],
            [
                'name' => 'Sov- & mysplagg',
                'key_word' => '
                    Pyjamas, Morgonrock, Badrockar, Badrock, Nattlinne, Nattklänning,
                     Pajamas, Bathrobe, Bathrobes, Nightdress
                ',
                'sub_key_word' => 'Pyjamas, Morgonrockar, Badrockar, Nattlinnen, Nattklänningar'
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
                'sub_key_word' => 'Balaclavas, Barnklockor, Halsdukar, Handskar, Vantar, Hattar, Kepsar, Mössor, 
Paraplyer, Plånböcker, Scarfs, Smycken, Solglasögon, Bandanas, Diadem, Hårband
Nyckelringslampa, Nyckelring'
            ],
        ];

        foreach ($configurations as $configuration)
        {
            $categoryConfigurations = new CategoryConfigurations();

            $categoryConfigurations
                ->setCustomCategoryName($configuration['name'])
                ->setKeyWords($configuration['key_word'])
                ->setSubKeyWords($configuration['sub_key_word']);
            $manager->persist($categoryConfigurations);
        }

        $manager->flush();
    }
}
