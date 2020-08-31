<?php

namespace App\Services\Models;

use App\Entity\Shop;
use App\Services\Models\Shops\Adrecord\BabyBjornService;
use App\Services\Models\Shops\Adrecord\CardooniaService;
use App\Services\Models\Shops\Adrecord\EbbeKids;
use App\Services\Models\Shops\Adrecord\FrankDandy;
use App\Services\Models\Shops\Adrecord\GusTextil;
use App\Services\Models\Shops\Adrecord\Jultroja;
use App\Services\Models\Shops\Adrecord\LeksakscityService;
use App\Services\Models\Shops\Adrecord\NallerietService;
use App\Services\Models\Shops\Adrecord\NamnbandService;
use App\Services\Models\Shops\Adrecord\ShirtstoreService;
use App\Services\Models\Shops\Adrecord\SpelexpertenService;
use App\Services\Models\Shops\Adrecord\SportshopenService;
use App\Services\Models\Shops\Adrecord\StigaSportsService;
use App\Services\Models\Shops\Adrecord\StrumpgalenService;
use App\Services\Models\Shops\Adrecord\TwarService;
use App\Services\Models\Shops\AhlensService;
use App\Services\Models\Shops\BabyLandService;
use App\Services\Models\Shops\BabyShopService;
use App\Services\Models\Shops\BabyVService;
use App\Services\Models\Shops\BjornBorgService;
use App\Services\Models\Shops\COSService;
use App\Services\Models\Shops\CykloteketService;
use App\Services\Models\Shops\ElodiService;
use App\Services\Models\Shops\IdentityGroup;
use App\Services\Models\Shops\JollyRoomService;
use App\Services\Models\Shops\LekiaService;
use App\Services\Models\Shops\LekmerService;
use App\Services\Models\Shops\LindexService;
use App\Services\Models\Shops\LitenlekermerService;
use App\Services\Models\Shops\LitenlekerService;
use App\Services\Models\Shops\NikeService;
use App\Services\Models\Shops\PyretService;
use App\Services\Models\Shops\ReimaService;
use App\Services\Models\Shops\SneakersPointService;
use App\Services\Models\Shops\StorAndLitenService;
use App\Services\Models\Shops\TradeDoubler\SportamoreService;

class ManagerShopsService
{
//Todo grouping waiting 
//'vegaoo'
//'nordic_nest'

//'nepiece_nordic'
//'blue_tomato'
//'ellos_se' 
//'jd_sports' 

    /**
     * @var PyretService
     */
    private $polarn_pyret;

    /**
     * @var BabyShopService
     */
    private $babyshop;

    /**
     * @var JollyRoomService
     */
    private $jollyroom;

    /**
     * @var ReimaService
     */
    private $reima;

    /**
     * @var LekmerService
     */
    private $lekmer;

    /**
     * @var BabyLandService
     */
    private $babyland;

    /**
     * @var BabyVService
     */
    private $babyV;

    /**
     * @var ElodiService
     */
    private $elodi;

    /**
     * @var LindexService
     */
    private $lindex;

    /**
     * @var LitenlekermerService
     */
    private $litenlekermer;

    /**
     * @var CykloteketService
     */
    private $cykloteket;

    /**
     * @var AhlensService
     */
    private $ahlens;

    // Adrecord

    /**
     * @var BabyBjornService
     */
    private $baby_bjorn;

    /**
     * @var CardooniaService
     */
    private $cardoonia;

    /**
     * @var EbbeKids
     */
    private $ebbeKids;

    /**
     * @var FrankDandy
     */
    private $frankDandy;

    /**
     * @var Jultroja
     */
    private $jultroja;

    /**
     * @var GusTextil
     */
    private $gus_rextil;

    /**
     * @var LeksakscityService
     */
    private $leksakscity;

    /**
     * @var NallerietService
     */
    private $nalleriet;

    /**
     * @var NamnbandService
     */
    private $namnband;

    /**
     * @var ShirtstoreService
     */
    private $shirtstore;

    /**
     * @var SpelexpertenService
     */
    private $spelexperten;

    /**
     * @var SportshopenService
     */
    private $sportshopen;

    /**
     * @var StigaSportsService
     */
    private $stigaSports;

    /**
     * @var StrumpgalenService
     */
    private $strumpgalen;

    /**
     * @var TwarService
     */
    private $twar;

    /**
     * @var NikeService
     */
    private $nike;

    /**
     * @var LitenlekerService
     */
    private $litenleker;

    /**
     * @var COSService
     */
    private $cos;

    /**
     * @var BjornBorgService
     */
    private $bjorn_borg;

    /**
     * @var LekiaService
     */
    private $lekia;
    
    /**
     * @var SneakersPointService
     */
    private $sneakersPoint;

    /**
     * @var StorAndLitenService
     */
    private $stor_and_liten;

    /**
     * @var SportamoreService
     */
    private $sportamore;

    /**
     * ManagerShopsService constructor.
     * @param PyretService $polarn_pyret
     * @param BabyShopService $babyshop
     * @param JollyRoomService $jollyroom
     * @param ReimaService $reima
     * @param LekmerService $lekmer
     * @param BabyLandService $babyland
     * @param BabyVService $babyV
     * @param ElodiService $elodi
     * @param LindexService $lindex
     * @param LitenlekermerService $litenlekermer
     * @param CykloteketService $cykloteket
     * @param AhlensService $ahlens
     * @param BabyBjornService $baby_bjorn
     * @param CardooniaService $cardoonia
     * @param EbbeKids $ebbeKids
     * @param FrankDandy $frankDandy
     * @param Jultroja $jultroja
     * @param GusTextil $gus_rextil
     * @param LeksakscityService $leksakscity
     * @param NallerietService $nalleriet
     * @param NamnbandService $namnband
     * @param ShirtstoreService $shirtstore
     * @param SpelexpertenService $spelexperten
     * @param SportshopenService $sportshopen
     * @param StigaSportsService $stigaSports
     * @param StrumpgalenService $strumpgalen
     * @param TwarService $twar
     * @param NikeService $nike
     * @param LitenlekerService $litenleker
     * @param COSService $cos
     * @param BjornBorgService $bjorn_borg
     * @param LekiaService $lekia
     * @param SneakersPointService $sneakersPoint
     * @param StorAndLitenService $stor_and_liten
     * @param SportamoreService $sportamore
     */
    public function __construct(PyretService $polarn_pyret, BabyShopService $babyshop, JollyRoomService $jollyroom, ReimaService $reima, LekmerService $lekmer, BabyLandService $babyland, BabyVService $babyV, ElodiService $elodi, LindexService $lindex, LitenlekermerService $litenlekermer, CykloteketService $cykloteket, AhlensService $ahlens, BabyBjornService $baby_bjorn, CardooniaService $cardoonia, EbbeKids $ebbeKids, FrankDandy $frankDandy, Jultroja $jultroja, GusTextil $gus_rextil, LeksakscityService $leksakscity, NallerietService $nalleriet, NamnbandService $namnband, ShirtstoreService $shirtstore, SpelexpertenService $spelexperten, SportshopenService $sportshopen, StigaSportsService $stigaSports, StrumpgalenService $strumpgalen, TwarService $twar, NikeService $nike, LitenlekerService $litenleker, COSService $cos, BjornBorgService $bjorn_borg, LekiaService $lekia, SneakersPointService $sneakersPoint, StorAndLitenService $stor_and_liten, SportamoreService $sportamore)
    {
        $this->polarn_pyret = $polarn_pyret;
        $this->babyshop = $babyshop;
        $this->jollyroom = $jollyroom;
        $this->reima = $reima;
        $this->lekmer = $lekmer;
        $this->babyland = $babyland;
        $this->babyV = $babyV;
        $this->elodi = $elodi;
        $this->lindex = $lindex;
        $this->litenlekermer = $litenlekermer;
        $this->cykloteket = $cykloteket;
        $this->ahlens = $ahlens;
        $this->baby_bjorn = $baby_bjorn;
        $this->cardoonia = $cardoonia;
        $this->ebbeKids = $ebbeKids;
        $this->frankDandy = $frankDandy;
        $this->jultroja = $jultroja;
        $this->gus_rextil = $gus_rextil;
        $this->leksakscity = $leksakscity;
        $this->nalleriet = $nalleriet;
        $this->namnband = $namnband;
        $this->shirtstore = $shirtstore;
        $this->spelexperten = $spelexperten;
        $this->sportshopen = $sportshopen;
        $this->stigaSports = $stigaSports;
        $this->strumpgalen = $strumpgalen;
        $this->twar = $twar;
        $this->nike = $nike;
        $this->litenleker = $litenleker;
        $this->cos = $cos;
        $this->bjorn_borg = $bjorn_borg;
        $this->lekia = $lekia;
        $this->sneakersPoint = $sneakersPoint;
        $this->stor_and_liten = $stor_and_liten;
        $this->sportamore = $sportamore;
    }

    public function __call($name, $arguments)
    {
        $prepareProperty = Shop::getMapShopNameByKey($name);

        if (property_exists($this, $prepareProperty)) {
            $this->getProprtyObject($prepareProperty)
                ->identityGroupColumn(array_shift($arguments));
        }
    }

    /**
     * @param string $property
     * @return IdentityGroup
     */
    private function getProprtyObject(string $property)
    {
        return $this->$property;
    }
}