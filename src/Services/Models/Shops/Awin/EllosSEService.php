<?php


namespace App\Services\Models\Shops\Awin;

use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;
use App\Services\Models\Shops\Strategies\CutLastSomeDigitFromEan;
use App\Services\Models\Shops\Strategies\CutLastSomeDigitFromSkuAndSomeFromEan;
use App\Services\Models\Shops\Strategies\CutLastSomeDigitFromSku;
use App\Services\Models\Shops\Strategies\FullProductName;
use App\Services\Models\Shops\Strategies\CutTwoWordFromProductName;
use App\Services\Models\Shops\Strategies\CutLastSomeDigitFromSkuAndFullName;
use App\Services\Models\Shops\Strategies\CutLastSomeDigitFromSkuAndEanAndFirstWordFromName;
use App\Services\Models\Shops\Strategies\CutLastSomeDigitFromEanAndFullName;

class EllosSEService implements IdentityGroup
{
    private $identityBrand = [];

    /**
     * EllosSEService constructor.
     * @param array $identityBrand
     */
    public function __construct()
    {
        $this->identityBrand = $this->identityBrand();
    }

    public function identityGroupColumn(Product $product)
    {
        if (array_key_exists($product->getBrand(), $this->identityBrand)) {
            $strategy = $this->identityBrand[$product->getBrand()];
        } else {
            $strategy = new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -2);
        }
        $strategy($product);
    }

    public function identityBrand()
    {
        return [
            "8848 Altitude" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -2),
            "Adidas Originals" => new CutLastSomeDigitFromEan(-2),
            "Adidas Sport Performance" => new FullProductName(),
            "Alf" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Alga" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "American Tourister" => new CutLastSomeDigitFromEan(-2),
            "Aquaplay" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Aquarapid" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "BB Junior" => new CutLastSomeDigitFromEan(-2),
            "BEX" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "BIG" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "BRIO" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Babblarna" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Baby Alive" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Baby Born" => new CutLastSomeDigitFromEan(-2),
            "Baby Dan" => new CutLastSomeDigitFromEan(-2),
            "Bagheera" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Bamse" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Barbapapa" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Batman" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Bestway" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Beurer" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Bfriends" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "BiBaBad" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Billieblush" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Birkenstock" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Björn Borg" => new FullProductName(),
            "Black & Decker" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Black Ice" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Bloomingville" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Blundstone" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Bob The Builder" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Bolibompa" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Brandman Sam" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Brunngård" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Bullyland" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Burton" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "CAT" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "CR7" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Candide" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Carlobaby" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Celly" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Champion" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Chi Chi Love" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Civiliants" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Clementoni" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Clicko" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Cold" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Color kids" => new FullProductName(), "Converse" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Craft" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Creamie" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "DC Shoes" => new FullProductName(), "DUDE Packaging" => new CutTwoWordFromProductName(), "Danspil" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "DeltaBaby" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Denver" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "DesignaFriend" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Dickie Toys" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Didriksons" => new CutTwoWordFromProductName(), "Disney" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Done by Deer" => new CutLastSomeDigitFromEan(-2), "Doomoo" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Duffy" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "ECCO" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "EN FANT" => new FullProductName(), "Egmont Kärnan" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Ellos" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Elodie Details" => new CutLastSomeDigitFromSkuAndSomeFromEan(-3, -3),
            "Eskimo" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Estelle & Thild" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Evi" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "FIXONI" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Fabbrix" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Fila" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Fireman Sam" => new CutLastSomeDigitFromEan(-2), "Fisher Price" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Fitbit" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Franklin" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "G-Star" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "GP" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Galt" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Gamesson" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Geggamoja" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Geomag" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Get & Go" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Globen lighting" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Gulliver" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Happy Baby" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Happy Friend" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Happy Hands" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Happy Pets" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Hasbro" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Hasta" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Helly Hansen" => new FullProductName(), "Heroes of the city" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Hestra" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "How to Kiss a Frog" => new CutLastSomeDigitFromEan(-2), "Hulabalu" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Hummel" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Hunter" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Hust & Claire" => new FullProductName(),
            "I dig denim" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Intex" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "JCB" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "JaBaDaBaDo" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Jack & jones" => new CutLastSomeDigitFromSkuAndSomeFromEan(-3, -2), "Jakks pacific" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Juicy Couture" => new FullProductName(), "KIDS ONLY" => new CutLastSomeDigitFromSkuAndFullName(-2), "Kaktus" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Kalas" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Kavat" => new FullProductName(), "Kids Concept" => new CutLastSomeDigitFromSkuAndEanAndFirstWordFromName(-2, -2),
            "Klippex" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Kombi" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "L.O.L Surprise!" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "LMTD" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "La Redoute" => new CutLastSomeDigitFromSkuAndFullName(-3), "Leaf" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Lego" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Leila's General Store" => new CutLastSomeDigitFromSkuAndFullName(-1), "Levi's" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Lil" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Atelier" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Lindberg" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Little Tikes" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Lumo Stars" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Lundby" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Lyle & Scott" => new FullProductName(), "MESSI" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "MOJ SWEDEN" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "MX Lek" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Magic Tracks" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Majorette" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Majvillan" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "MarMar Copenhagen" => new CutLastSomeDigitFromSkuAndFullName(-2), "Markslöjd" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Marvel" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Marvel avengers" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Masha and the Bear" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Me Too" => new CutLastSomeDigitFromSkuAndFullName(-2), "Meiya & Alvin" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Merrell" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Micki" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Milly & Willy" => new FullProductName(),
            "Mindtwister" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Mumin" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Munchkin" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Music" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "My Little Pony" => new FullProductName(), "NG Baby" => new CutLastSomeDigitFromEan(-2), "NORDFORM" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Name it" => new CutLastSomeDigitFromSkuAndFullName(-3), "Nattou" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Navy Stories" => new CutLastSomeDigitFromSku(-2), "New Balance" => new FullProductName(), "New era" => new CutLastSomeDigitFromEanAndFullName(-2), "Nijdam" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Ninco" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Nivea" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Noa Noa" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Noppies" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Nordic Hoj" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "North" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "ON OUR TERMS" => new CutLastSomeDigitFromEanAndFullName(-2), "Oral B" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Oskar & Ellen" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Outgame" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "PJ Masks" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "PLANES" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "PLAYMOBIL" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Pabobo" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Paw Patrol" => new CutLastSomeDigitFromSku(-2), "Peliko" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Peppa Pig" => new CutLastSomeDigitFromSku(-2), "Peppy Pals" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Petrol" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Pino" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Pippi" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Pixi" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Play-Doh" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Playbox" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Playgro" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Plus Plus" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Polecat" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Puma" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Quercetti" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Quiksilver" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Rastar" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Ravensburger" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Reebok Classic" => new FullProductName(),
            "Reebok Performance" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Reima" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Replay" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Rock On" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Roxy" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Rubber Duck" => new CutLastSomeDigitFromEanAndFullName(-3), "Russell Athletic" => new CutLastSomeDigitFromEanAndFullName(-3), "Rätt Start" => new CutLastSomeDigitFromEan(-2), "STATE OF WOW" => new CutLastSomeDigitFromSku(-2), "Salomon" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Samsonite" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Saucony" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Scotch & R’belle" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Scotch Shrunk" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Seger" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Shaun The Sheep" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Shepherd" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Simba Dickie" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Skip Hop" => new CutLastSomeDigitFromSku(-2), "Skrållan" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "SmART Pixelator" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Small Rags" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Smart Sketcher" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Smiling Shark" => new CutLastSomeDigitFromSku(-2), "SootheTime" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Sorel" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Speedo" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Spiderman" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Sportme" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Springyard" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "SpyX" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Star Trading" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Steffi" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Stiga" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Sunsport" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Superfit" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Superga" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Svea" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Swimpy" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Sylvanian Families" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Syma" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "TOMS" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Tactic" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Teddykompaniet" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "The white brand" => new FullProductName(), "Ticket to Heaven" => new CutLastSomeDigitFromSku(-1), "Tildas" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Timberland" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Topcom" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Tove Frank" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Transformers" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Trefl" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Tretorn" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Troll" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Trunki" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "U.S. Polo Assn." => new CutLastSomeDigitFromEanAndFullName(-2), "Udeas" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Ugg" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Upfront" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Vans" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Ver" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Vertbaudet" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Viking" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Viking Toys" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Vtwonen" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "WOOOD" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Warmies" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "WearColour" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Weleda" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Wildflower" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3),
            "Zunblock" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3), "Áhkká" => new CutLastSomeDigitFromSkuAndSomeFromEan(-2, -3)
        ];
    }
}

//class CutLastSomeDigitFromSkuAndSomeFromEan extends CutLastSomeDigitFromEan
//{
//    protected $cutFromSku;
//
//    /**
//     * CutLastSomeDigitFromSkuAndSomeFromEan constructor.
//     * @param $cutFromEan
//     * @param $cutFromSku
//     */
//    public function __construct($cutFromEan, $cutFromSku)
//    {
//        parent::__construct($cutFromEan);
//        $this->cutFromSku = $cutFromSku;
//    }
//
//
//    public function __invoke(Product $product)
//    {
//        parent::__invoke($product);
//        $sku = $product->getSku();
//        if (strlen($sku)) {
//            $product->setGroupIdentity(
//                $product->getGroupIdentity() .
//                '_' . mb_substr($sku, 0, $this->cutFromSku)
//            );
//        }
//    }
//}
//
//class CutLastSomeDigitFromSkuAndEanAndFirstWordFromName extends CutLastSomeDigitFromSkuAndSomeFromEan
//{
//
//    /**
//     * CutLastSomeDigitFromSkuAndEanAndFirstWordFromName constructor.
//     * @param $cutFromEan
//     * @param $cutFromSku
//     */
//    public function __construct($cutFromEan, $cutFromSku)
//    {
//        parent::__construct($cutFromEan, $cutFromSku);
//    }
//
//    public function __invoke(Product $product)
//    {
//        parent::__invoke($product);
//        $name = $product->getName();
//        if (strlen($name)) {
//            $preg_split = preg_split('/[\s,\/]+/', $name, 2);
//            if (count($preg_split)) {
//                $array_slice = array_slice($preg_split, 0, 1);
//                if (count($array_slice)) {
//                    $product->setGroupIdentity(
//                        $product->getGroupIdentity() . '_' . mb_strtolower(implode('_', $array_slice))
//                    );
//                }
//            }
//        }
//    }
//}
//
//class CutLastSomeDigitFromSkuAndFullName extends CutLastSomeDigitFromSku
//{
//    /**
//     * CutLastSomeDigitFromSkuAndFullName constructor.
//     * @param int $cutFromSku
//     */
//    public function __construct(int $cutFromSku)
//    {
//        parent::__construct($cutFromSku);
//    }
//
//    public function __invoke(Product $product)
//    {
//        parent::__invoke($product);
//        $name = $product->getName();
//        if (strlen($name)) {
//            $mb_strtolower = mb_strtolower(preg_replace('/[\s+,.]+/', '', $name));
//            $product->setGroupIdentity($product->getGroupIdentity() . '_' . $mb_strtolower);
//        }
//    }
//}
//
//class CutLastSomeDigitFromEanAndFullName extends CutLastSomeDigitFromEan
//{
//    /**
//     * CutLastSomeDigitFromEanAndFullName constructor.
//     * @param int $cut
//     */
//    public function __construct(int $cut)
//    {
//        parent::__construct($cut);
//    }
//
//    public function __invoke(Product $product)
//    {
//        parent::__invoke($product);
//        $name = $product->getName();
//        if (strlen($name)) {
//            $mb_strtolower = mb_strtolower(preg_replace('/[\s+,.]+/', '', $name));
//            $product->setGroupIdentity($product->getGroupIdentity() .
//                '_' . $mb_strtolower);
//        }
//    }
//}
//
//
//class CutLastSomeDigitFromEan
//{
//
//    protected $cutFromEan;
//
//    /**
//     * CutLastSomeDigitFromEan constructor.
//     * @param $cutFromEan
//     */
//    public function __construct($cutFromEan)
//    {
//        $this->cutFromEan = $cutFromEan;
//    }
//
//    public function __invoke(Product $product)
//    {
//        $ean = $product->getEan();
//        if (strlen($ean)) {
//            $product->setGroupIdentity(mb_substr($ean, 0, $this->cutFromEan));
//        }
//    }
//}
//
//class CutLastSomeDigitFromSku
//{
//
//    protected $cutFromSku;
//
//    /**
//     * CutLastSomeDigitFromSku constructor.
//     * @param $cutFromSku
//     */
//    public function __construct($cutFromSku)
//    {
//        $this->cutFromSku = $cutFromSku;
//    }
//
//
//    public function __invoke(Product $product)
//    {
//        $sku = $product->getSku();
//        if (strlen($sku)) {
//            $product->setGroupIdentity(mb_substr($sku, 0, $this->cutFromSku));
//        }
//    }
//}
//
//class FullProductName
//{
//    public function __invoke(Product $product)
//    {
//        $name = $product->getName();
//        $name = preg_replace('/[\s+,.]+/', '_', $name);
//
//        if (strlen($name)) {
//            $product->setGroupIdentity(mb_strtolower($name));
//        }
//    }
//}
//
//class CutTwoWordFromProductName
//{
//    public function __invoke(Product $product)
//    {
//        $preg_split = preg_split('/[\s,\/]+/', $product->getName(), 3);
//        if (count($preg_split) > 1) {
//            $array_slice = array_slice($preg_split, 0, 2);
//            if (count($array_slice)) {
//                $product->setGroupIdentity(mb_strtolower(implode('_', $array_slice)));
//            }
//        }
//    }
//}