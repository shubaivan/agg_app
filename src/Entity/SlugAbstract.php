<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation;
use App\Entity\Collection\Search\SearchProductCollection;

abstract class SlugAbstract implements SlugInterface
{
    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Annotation\Groups({
     *     Category::SERIALIZED_GROUP_LIST,
     *     Product::SERIALIZED_GROUP_LIST,
     *     Category::SERIALIZED_GROUP_RELATIONS_LIST,
     *     SearchProductCollection::GROUP_GET
     *     })
     */
    protected $slug;

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }
}