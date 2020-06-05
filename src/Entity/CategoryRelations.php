<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRelationsRepository")
 */
class CategoryRelations
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="subCategoryRelations", cascade={"persist"})
     * @Annotation\Groups({Category::SERIALIZED_GROUP_RELATIONS_LIST})
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     */
    private $subCategory;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="mainCategoryRelations", cascade={"persist"})
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     */
    private $mainCategory;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubCategory(): ?Category
    {
        return $this->subCategory;
    }

    public function setSubCategory(?Category $subCategory): self
    {
        $this->subCategory = $subCategory;

        return $this;
    }

    public function getMainCategory(): ?Category
    {
        return $this->mainCategory;
    }

    public function setMainCategory(?Category $mainCategory): self
    {
        $this->mainCategory = $mainCategory;

        return $this;
    }
}
