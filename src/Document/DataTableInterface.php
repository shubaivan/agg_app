<?php


namespace App\Document;


interface DataTableInterface
{
    public static function getImageColumns(): array;
    public static function getLinkColumns(): array;
    public static function getSeparateFilterColumn(): array;
    public static function getShortPreviewText(): array;
}