<?php

namespace App\Services\Models;

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

    /**
     * ManagerShopsService constructor.
     * @param PyretService $opyret
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
     */
    public function __construct(
        PyretService $opyret,
        BabyShopService $babyshop,
        JollyRoomService $jollyroom,
        ReimaService $reima,
        LekmerService $lekmer,
        BabyLandService $babyland,
        BabyVService $babyv,
        ElodiService $elodi,
        LindexService $lindex,
        LitenlekermerService $litenlekermer,
        CykloteketService $cykloteket,
        AhlensService $ahlens
    )
    {
        $this->pyret = $opyret;
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
    }

    public function __call($name, $arguments)
    {
        $prepareProperty = mb_strtolower($name);
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