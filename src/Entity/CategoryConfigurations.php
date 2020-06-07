<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryConfigurationsRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="entity_that_rarely_changes")
 */
class CategoryConfigurations
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Category
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     * @ORM\OneToOne(targetEntity="Category", inversedBy="categoryConfigurations")
     */
    private $categoryId;

    /**
     * @ORM\Column(type="text")
     */
    private $keyWords;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKeyWords(): ?string
    {
        return $this->keyWords;
    }

    public function setKeyWords(string $keyWords): self
    {
        $this->keyWords = preg_replace('/\s+/', '', $keyWords);

        return $this;
    }

    public function getCategoryId(): ?Category
    {
        return $this->categoryId;
    }

    public function setCategoryId(?Category $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }
}