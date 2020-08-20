<?php


namespace App\Document;


interface MatchSameProducts
{
    public function getShop();

    public function getName();

    public function getBrand();
    
    public function getEan();

    public function getAttributeByName(string $nameAttribute);
}