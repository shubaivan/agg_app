<?php


namespace App\EventListener;

use App\Entity\SlugAbstract;
use App\Entity\SlugForMatch;
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
        $cs->addRule('&', 'and');
        $cs->addRule('->', '_own_');
        $this->cs = $cs;
    }

    protected function applySlugToEntity(SlugAbstract $abstract)
    {
        $dataFroSlug = $abstract->getDataFroSlug();
        $abstract->setSlug($this->generateSlugForString($dataFroSlug));
    }

    protected function applySlugForMatchToEntity(SlugForMatch $abstract)
    {
        $dataFroSlug = $abstract->getDataFroSlugForMatch();
        $abstract->setSlugForMatch($this->generateSlugForString($dataFroSlug));
    }

    protected function generateSlugForString(string $someString)
    {
        return $this->cs->slugify($someString);
    }
}