<?php

namespace App\Services\Models;

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
use App\Services\Models\Shops\CykloteketService;
use App\Services\Models\Shops\ElodiService;
use App\Services\Models\Shops\IdentityGroup;
use App\Services\Models\Shops\JollyRoomService;
use App\Services\Models\Shops\LekmerService;
use App\Services\Models\Shops\LindexService;
use App\Services\Models\Shops\LitenlekermerService;
use App\Services\Models\Shops\PyretService;
use App\Services\Models\Shops\ReimaService;

class ManagerShopsService
{
    /**
     * @var PyretService
     */
    private $pyret;

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
    private $babyv;

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
    private $babybjorn;

    /**
     * @var CardooniaService
     */
    private $cardoonia;

    /**
     * @var EbbeKids
     */
    private $ebbekids;

    /**
     * @var FrankDandy
     */
    private $frankdandy;

    /**
     * @var Jultroja
     */
    private $jultroja;

    /**
     * @var GusTextil
     */
    private $gustextil;

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
    private $stigasports;

    /**
     * @var StrumpgalenService
     */
    private $strumpgalen;

    /**
     * @var TwarService
     */
    private $twar;

    /**
     * ManagerShopsService constructor.
     * @param PyretService $pyret
     * @param BabyShopService $babyshop
     * @param JollyRoomService $jollyroom
     * @param ReimaService $reima
     * @param LekmerService $lekmer
     * @param BabyLandService $babyland
     * @param BabyVService $babyv
     * @param ElodiService $elodi
     * @param LindexService $lindex
     * @param LitenlekermerService $litenlekermer
     * @param CykloteketService $cykloteket
     * @param AhlensService $ahlens
     * @param BabyBjornService $babybjorn
     * @param CardooniaService $cardoonia
     * @param EbbeKids $ebbekids
     * @param FrankDandy $frankdandy
     * @param Jultroja $jultroja
     * @param GusTextil $gustextil
     * @param LeksakscityService $leksakscity
     * @param NallerietService $nalleriet
     * @param NamnbandService $namnband
     * @param ShirtstoreService $shirtstore
     * @param SpelexpertenService $spelexperten
     * @param SportshopenService $sportshopen
     * @param StigaSportsService $stigasports
     * @param StrumpgalenService $strumpgalen
     * @param TwarService $twar
     */
    public function __construct(PyretService $pyret, BabyShopService $babyshop, JollyRoomService $jollyroom, ReimaService $reima, LekmerService $lekmer, BabyLandService $babyland, BabyVService $babyv, ElodiService $elodi, LindexService $lindex, LitenlekermerService $litenlekermer, CykloteketService $cykloteket, AhlensService $ahlens, BabyBjornService $babybjorn, CardooniaService $cardoonia, EbbeKids $ebbekids, FrankDandy $frankdandy, Jultroja $jultroja, GusTextil $gustextil, LeksakscityService $leksakscity, NallerietService $nalleriet, NamnbandService $namnband, ShirtstoreService $shirtstore, SpelexpertenService $spelexperten, SportshopenService $sportshopen, StigaSportsService $stigasports, StrumpgalenService $strumpgalen, TwarService $twar)
    {
        $this->pyret = $pyret;
        $this->babyshop = $babyshop;
        $this->jollyroom = $jollyroom;
        $this->reima = $reima;
        $this->lekmer = $lekmer;
        $this->babyland = $babyland;
        $this->babyv = $babyv;
        $this->elodi = $elodi;
        $this->lindex = $lindex;
        $this->litenlekermer = $litenlekermer;
        $this->cykloteket = $cykloteket;
        $this->ahlens = $ahlens;
        $this->babybjorn = $babybjorn;
        $this->cardoonia = $cardoonia;
        $this->ebbekids = $ebbekids;
        $this->frankdandy = $frankdandy;
        $this->jultroja = $jultroja;
        $this->gustextil = $gustextil;
        $this->leksakscity = $leksakscity;
        $this->nalleriet = $nalleriet;
        $this->namnband = $namnband;
        $this->shirtstore = $shirtstore;
        $this->spelexperten = $spelexperten;
        $this->sportshopen = $sportshopen;
        $this->stigasports = $stigasports;
        $this->strumpgalen = $strumpgalen;
        $this->twar = $twar;
    }


    public function __call($name, $arguments)
    {
        $prepareProperty = mb_strtolower($name);

        $patterns = array();
        $patterns[] = '/.se/';
        $patterns[] = '/ö/';
        $patterns[] = '/ /';
        $patterns[] = '/åhlens/';

        $replacements = array();

        $replacements[] = '';
        $replacements[] = 'o';
        $replacements[] = '';
        $replacements[] = 'ahlens';

        $prepareProperty = preg_replace($patterns, $replacements, $prepareProperty);

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