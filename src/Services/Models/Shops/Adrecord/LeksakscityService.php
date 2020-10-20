<?php


namespace App\Services\Models\Shops\Adrecord;


use App\Entity\Product;
use App\Services\Models\Shops\IdentityGroup;
use App\Services\Models\Shops\Strategies\CutSomeDigitFromSkuAndFullName;
use App\Services\Models\Shops\Strategies\CutSomeBlocksByDelimiterFromSku;

class LeksakscityService implements IdentityGroup
{
    /**
     * @var array
     */
    private $identityBrand = [];

    /**
     * LeksakscityService constructor.
     */
    public function __construct()
    {
        $this->identityBrand = $this->identityBrand();
    }

    /**
     * @param Product $product
     *
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fset-med-paerlor-i-olika-faerger-och-moenster-foer-att-goera-egna-smycken-samt-paerlor-och-paerlplattor%2F3131-melissa-doug-diy-parlkit-hjarta-45-delar-0000772194952.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fset-med-paerlor-i-olika-faerger-och-moenster-foer-att-goera-egna-smycken-samt-paerlor-och-paerlplattor%2F3130-melissa-doug-sparkling-flowers-parlkit-45-delar-0000772194945.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fbarnpussel-och-traepussel-foer-barn-fran-melissa-doug%2F3268-melissa-doug-knoppussel-bondgardsdjur-0000772190503.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fset-med-paerlor-i-olika-faerger-och-moenster-foer-att-goera-egna-smycken-samt-paerlor-och-paerlplattor%2F2772-melissa-doug-parlor-i-tra-i-fina-farger-120-delar-0000772141796.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fset-med-paerlor-i-olika-faerger-och-moenster-foer-att-goera-egna-smycken-samt-paerlor-och-paerlplattor%2F4673-melissa-doug-parlor-flower-power-120-delar-0000772141789.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fset-med-paerlor-i-olika-faerger-och-moenster-foer-att-goera-egna-smycken-samt-paerlor-och-paerlplattor%2F3132-melissa-doug-sweet-hearts-traparlor-120-delar-0000772141758.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fset-med-paerlor-i-olika-faerger-och-moenster-foer-att-goera-egna-smycken-samt-paerlor-och-paerlplattor%2F2774-melissa-doug-parlor-i-tra-i-fina-farger-220-delar-0000772141697.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fleksakstarta-och-leksaksmat-tarta-i-trae-med-ljus-till-barn-handla-leksakstartor-billigt%2F4672-melissa-doug-tarta-i-tre-lager-0000772140690.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fett-brett-sortiment-av-leksaksfrukt-och-leksaksgroensaker-foer-koekslek%2F2773-melissa-doug-leksaksmat-frukter-i-tralada-0000772140218.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fbarnpussel-och-traepussel-foer-barn-fran-melissa-doug%2F3267-melissa-doug-pussel-bondgard-chunky-med-tjocka-bitar-0000772137232.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fbarnpussel-och-traepussel-foer-barn-fran-melissa-doug%2F2771-melissa-doug-pussel-old-mcdonald-bondgard-i-tra-med-ljud-0000772107389.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fbroed-leksaksmat-leksaksbroed-baguetter-och-rostbroed-samt-aeven-palaegg-och-leksaksbroedrostar-till-barn-fran-melissa-and-doug%2F2949-melissa-doug-leksaksmat-smorgas-kit-tramackor-0000772105132.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fbroed-leksaksmat-leksaksbroed-baguetter-och-rostbroed-samt-aeven-palaegg-och-leksaksbroedrostar-till-barn-fran-melissa-and-doug%2F2950-melissa-doug-brod-frukt-och-gronsaker-0000772104876.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fhallbar-leksaksmat-som-aer-tillverkad-av-trae-tyg-eller-plast-perfekt-till-barnens-koekslekar-med-leksakskoek%2F2951-melissa-doug-leksaksmat-matvaror-med-forvaring-0000772102711.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fhallbar-leksaksmat-som-aer-tillverkad-av-trae-tyg-eller-plast-perfekt-till-barnens-koekslekar-med-leksakskoek%2F2775-melissa-doug-pizza-i-tra-lekaksmat-54-delar-0000772101677.html
     *
     *
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fbruder-figurer-som-kan-koera-maskiner-fran-bruder-och-hantera-tillbehoer%2F3770-bruder-figur-med-soptunna-sopborste-och-spade-62140-4001702621407.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fbruder-figurer-som-kan-koera-maskiner-fran-bruder-och-hantera-tillbehoer%2F3768-bruder-figur-med-skottkarra-spade-och-sopborste-62130-4001702621308.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Fbruder-figurer-som-kan-koera-maskiner-fran-bruder-och-hantera-tillbehoer%2F2567-bruder-figur-med-domkraft-och-verktyg-62100-4001702621001.html
     *
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Friddjur%2F1491-mekanisk-riddjur-bock-berga-sitthojd-54-cm-6950587835509.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Friddjur%2F1494-mekaniskt-leksaksdjur-lejonet-simba-sitthojd-54-cm-6999593000730.html
     * https://click.adrecord.com/?p=877&c=37741&url=https%3A%2F%2Fwww.leksakscity.se%2Friddjur%2F1493-mekaniskt-leksaksdjur-kamelen-oki-sitthojd-54-cm-6950587835516.html
     *
     * @return Product|mixed
     */
    public function identityGroupColumn(Product $product)
    {
        if (array_key_exists($product->getBrand(), $this->identityBrand)) {
            $strategy = $this->identityBrand[$product->getBrand()];
        } else {
            $sku = $product->getSku();
            $explodeSku = explode('-', $sku);
            if ($explodeSku && $product->getProductUrl()) {
                $firstEl = array_shift($explodeSku);
                $parts = parse_url($product->getProductUrl());
                if (isset($parts['query'])) {
                    parse_str($parts['query'], $query);
                    if (isset($query['url'])) {
                        if (preg_match('/([^\/]+$)/', $query['url'], $matches)) {
                            $identity = array_shift($matches);
                            $identityReplace = preg_replace('/\.html/', '', $identity);
                            $identityExplode = explode('-', $identityReplace);
                            if ($identityExplode) {
                                $lastEl = array_pop($identityExplode);
                                $identity = mb_substr($lastEl, 0, 9);

                                $product->setGroupIdentity($firstEl . '_' . $identity);
                            }
                        }
                    }
                }
            }

            return $product;
        }
        $strategy($product);
    }

    public function identityBrand()
    {
        return [
            "Dino Cars" => new CutSomeDigitFromSkuAndFullName(-2),
            "Euro Play" => new CutSomeBlocksByDelimiterFromSku(2, '-'), "Feilun" => new CutSomeBlocksByDelimiterFromSku(2, '-'), "FurReal" => new CutSomeBlocksByDelimiterFromSku(2, '-'), "Gear4Play" => new CutSomeBlocksByDelimiterFromSku(2, '-'), "Injusa" => new CutSomeBlocksByDelimiterFromSku(2, '-'), "Joueco" => new CutSomeBlocksByDelimiterFromSku(2, '-'), "Jovi" => new CutSomeBlocksByDelimiterFromSku(2, '-'), "Jurassic World" => new CutSomeBlocksByDelimiterFromSku(2, '-'), "Nikko" => new CutSomeBlocksByDelimiterFromSku(2, '-'), "Silverlit" => new CutSomeBlocksByDelimiterFromSku(2, '-'), "Skrållan & Lillan" => new CutSomeBlocksByDelimiterFromSku(2, '-'), "Take Me Home" => new CutSomeBlocksByDelimiterFromSku(2, '-'), "Woodi World Toy" => new CutSomeBlocksByDelimiterFromSku(2, '-'), "XQ RC Toys" => new CutSomeBlocksByDelimiterFromSku(2, '-'), "Zuro Robo Alive"  => new CutSomeBlocksByDelimiterFromSku(2, '-')
        ];
    }
}