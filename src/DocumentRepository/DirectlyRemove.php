<?php


namespace App\DocumentRepository;


interface DirectlyRemove
{
    public function removeByShop(
        string $collection,
        string $shop
    );
}