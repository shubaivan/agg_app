<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategorySectionRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="entity_that_rarely_changes")
 */
class CategorySection
{
    const SERIALIZED_GROUP_LIST = 'section_group_list';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Annotation\Groups({CategorySection::SERIALIZED_GROUP_LIST})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Annotation\Groups({Category::SERIALIZED_GROUP_RELATIONS_LIST, CategorySection::SERIALIZED_GROUP_LIST})
     */
    private $sectionName;

    /**
     * @var Category[]|Collection
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     * @ORM\OneToMany(targetEntity="Category", mappedBy="sectionRelation",fetch="LAZY")
     */
    private $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSectionName(): ?string
    {
        return $this->sectionName;
    }

    public function setSectionName(string $sectionName): self
    {
        $this->sectionName = $sectionName;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        if (!$this->categories) {
            $this->categories = new ArrayCollection();
        }
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->getCategories()->contains($category)) {
            $this->categories[] = $category;
            $category->setSectionRelation($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->getCategories()->contains($category)) {
            $this->categories->removeElement($category);
            // set the owning side to null (unless already changed)
            if ($category->getSectionRelation() === $this) {
                $category->setSectionRelation(null);
            }
        }

        return $this;
    }
}
