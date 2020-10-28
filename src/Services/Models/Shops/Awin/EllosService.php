<?php


namespace App\Services\Models\Shops\Awin;

use App\Entity\Product;
use App\Services\Models\Shops\AbstractShop;
use App\Services\Models\Shops\IdentityGroup;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEan;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSkuAndSomeFromEan;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSku;
use App\Services\Models\Shops\Strategies\CutSomeWordsFromProductNameByDelimiter;
use App\Services\Models\Shops\Strategies\FullProductName;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSkuAndFullName;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSkuAndEanAndSomeWordFromName;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromEanAndFullName;

class EllosService extends AbstractShop
{
    /**
     * @param Product $product
     * @return bool|mixed|void
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function identityGroupColumn(Product $product)
    {
        $parentResult = parent::identityGroupColumn($product);
        if ($parentResult) {
            return;
        }

        if (array_key_exists($product->getBrand(), $this->identityBrand)) {
            $strategy = $this->identityBrand[$product->getBrand()];
        } else {
            $strategy = new CutSomeDigitFromSkuAndSomeFromEan(-2, -2);
        }

        $strategy($product);
    }

    public function identityBrand()
    {
        return [
            "8848 Altitude" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -2),
            "Adidas Originals" => new CutSomeDigitFromEan(-2),
            "Adidas Sport Performance" => new FullProductName(),
            "Alf" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Alga" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "American Tourister" => new CutSomeDigitFromEan(-2),
            "Aquaplay" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Aquarapid" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "BB Junior" => new CutSomeDigitFromEan(-2),
            "BEX" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "BIG" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "BRIO" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Babblarna" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Baby Alive" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Baby Born" => new CutSomeDigitFromEan(-2),
            "Baby Dan" => new CutSomeDigitFromEan(-2),
            "Bagheera" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Bamse" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Barbapapa" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Batman" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Bestway" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Beurer" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Bfriends" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "BiBaBad" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Billieblush" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Birkenstock" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Björn Borg" => new FullProductName(),
            "Black & Decker" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Black Ice" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Bloomingville" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Blundstone" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Bob The Builder" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Bolibompa" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Brandman Sam" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Brunngård" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Bullyland" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Burton" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "CAT" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "CR7" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Candide" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Carlobaby" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Celly" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Champion" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Chi Chi Love" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Civiliants" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Clementoni" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Clicko" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Cold" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Color kids" => new FullProductName(), "Converse" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Craft" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Creamie" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "DC Shoes" => new FullProductName(), "DUDE Packaging" => new CutSomeWordsFromProductNameByDelimiter(2), "Danspil" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "DeltaBaby" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Denver" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "DesignaFriend" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Dickie Toys" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Didriksons" => new CutSomeWordsFromProductNameByDelimiter(2), "Disney" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Done by Deer" => new CutSomeDigitFromEan(-2), "Doomoo" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Duffy" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "ECCO" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "EN FANT" => new FullProductName(), "Egmont Kärnan" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Ellos" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Elodie Details" => new CutSomeDigitFromSkuAndSomeFromEan(-3, -3),
            "Eskimo" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Estelle & Thild" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Evi" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "FIXONI" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Fabbrix" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Fila" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Fireman Sam" => new CutSomeDigitFromEan(-2), "Fisher Price" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Fitbit" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Franklin" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "G-Star" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "GP" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Galt" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Gamesson" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Geggamoja" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Geomag" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Get & Go" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Globen lighting" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Gulliver" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Happy Baby" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Happy Friend" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Happy Hands" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Happy Pets" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Hasbro" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Hasta" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Helly Hansen" => new FullProductName(), "Heroes of the city" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Hestra" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "How to Kiss a Frog" => new CutSomeDigitFromEan(-2), "Hulabalu" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Hummel" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Hunter" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Hust & Claire" => new FullProductName(),
            "I dig denim" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Intex" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "JCB" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "JaBaDaBaDo" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Jack & jones" => new CutSomeDigitFromSkuAndSomeFromEan(-3, -2), "Jakks pacific" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Juicy Couture" => new FullProductName(), "KIDS ONLY" => new CutSomeDigitFromSkuAndFullName(-2), "Kaktus" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Kalas" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Kavat" => new FullProductName(), "Kids Concept" => new CutSomeDigitFromSkuAndEanAndSomeWordFromName(-2, -2, 1),
            "Klippex" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Kombi" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "L.O.L Surprise!" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "LMTD" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "La Redoute" => new CutSomeDigitFromSkuAndFullName(-3), "Leaf" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Lego" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Leila's General Store" => new CutSomeDigitFromSkuAndFullName(-1), "Levi's" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Lil" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Atelier" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Lindberg" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Little Tikes" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Lumo Stars" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Lundby" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Lyle & Scott" => new FullProductName(), "MESSI" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "MOJ SWEDEN" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "MX Lek" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Magic Tracks" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Majorette" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Majvillan" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "MarMar Copenhagen" => new CutSomeDigitFromSkuAndFullName(-2), "Markslöjd" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Marvel" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Marvel avengers" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Masha and the Bear" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Me Too" => new CutSomeDigitFromSkuAndFullName(-2), "Meiya & Alvin" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Merrell" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Micki" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Milly & Willy" => new FullProductName(),
            "Mindtwister" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Mumin" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Munchkin" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Music" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "My Little Pony" => new FullProductName(), "NG Baby" => new CutSomeDigitFromEan(-2), "NORDFORM" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Name it" => new CutSomeDigitFromSkuAndFullName(-3), "Nattou" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Navy Stories" => new CutSomeDigitFromSku(-2), "New Balance" => new FullProductName(), "New era" => new CutSomeDigitFromEanAndFullName(-2), "Nijdam" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Ninco" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Nivea" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Noa Noa" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Noppies" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Nordic Hoj" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "North" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "ON OUR TERMS" => new CutSomeDigitFromEanAndFullName(-2), "Oral B" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Oskar & Ellen" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Outgame" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "PJ Masks" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "PLANES" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "PLAYMOBIL" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Pabobo" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Paw Patrol" => new CutSomeDigitFromSku(-2), "Peliko" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Peppa Pig" => new CutSomeDigitFromSku(-2), "Peppy Pals" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Petrol" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Pino" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Pippi" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Pixi" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Play-Doh" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Playbox" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Playgro" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Plus Plus" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Polecat" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Puma" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Quercetti" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Quiksilver" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Rastar" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Ravensburger" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Reebok Classic" => new FullProductName(),
            "Reebok Performance" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Reima" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Replay" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Rock On" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Roxy" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Rubber Duck" => new CutSomeDigitFromEanAndFullName(-3), "Russell Athletic" => new CutSomeDigitFromEanAndFullName(-3), "Rätt Start" => new CutSomeDigitFromEan(-2), "STATE OF WOW" => new CutSomeDigitFromSku(-2), "Salomon" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Samsonite" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Saucony" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Scotch & R’belle" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Scotch Shrunk" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Seger" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Shaun The Sheep" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Shepherd" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Simba Dickie" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Skip Hop" => new CutSomeDigitFromSku(-2), "Skrållan" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "SmART Pixelator" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Small Rags" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Smart Sketcher" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Smiling Shark" => new CutSomeDigitFromSku(-2), "SootheTime" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Sorel" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Speedo" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Spiderman" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Sportme" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Springyard" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "SpyX" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Star Trading" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Steffi" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Stiga" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Sunsport" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Superfit" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Superga" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Svea" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Swimpy" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Sylvanian Families" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Syma" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "TOMS" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Tactic" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Teddykompaniet" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "The white brand" => new FullProductName(), "Ticket to Heaven" => new CutSomeDigitFromSku(-1), "Tildas" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Timberland" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Topcom" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Tove Frank" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Transformers" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Trefl" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Tretorn" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Troll" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Trunki" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "U.S. Polo Assn." => new CutSomeDigitFromEanAndFullName(-2), "Udeas" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Ugg" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Upfront" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Vans" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Ver" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Vertbaudet" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Viking" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Viking Toys" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Vtwonen" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "WOOOD" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Warmies" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "WearColour" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Weleda" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Wildflower" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Zunblock" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3), "Áhkká" => new CutSomeDigitFromSkuAndSomeFromEan(-2, -3)
        ];
    }
}