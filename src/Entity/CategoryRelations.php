<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRelationsRepository")
 * @ORM\Table(
 *     uniqueConstraints={@UniqueConstraint(name="uniq_sub_and_main_index", columns={"sub_category_id", "main_category_id"})}
 *     )
 * @UniqueEntity(fields={"subCategory", "mainCategory"})
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
     * @ORM\Cache("NONSTRICT_READ_WRITE", region="categories_region")
     */
    private $subCategory;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="mainCategoryRelations", cascade={"persist"})
     * @ORM\Cache("NONSTRICT_READ_WRITE", region="categories_region")
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
