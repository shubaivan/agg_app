<?php


namespace App\EventListener;

use App\Entity\SlugAbstract;
use Cocur\Slugify\SlugifyInterface;


abstract class SlugApproach
{
    /**
     * @var
     */
    private $cs;

    /**
     * SlugApproach constructor.
     * @param $cs
     */
    public function __construct(SlugifyInterface $cs)
    {
        $this->cs = $cs;
    }

    protected function applySlugToEntity(SlugAbstract $abstract)
    {
        $dataFroSlug = $abstract->getDataFroSlug();
        $abstract->setSlug($this->cs->slugify($dataFroSlug));
        $t = 1;
    }
}