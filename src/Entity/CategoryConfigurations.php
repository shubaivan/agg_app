<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryConfigurationsRepository")
 * @ORM\Cache(
 *     usage="NONSTRICT_READ_WRITE",
 *     region="categories_region"
 * )
 *
 * @ORM\Table(name="category_configurations",
 *     indexes={
 *     @ORM\Index(name="category_configurations_sizes_idx", columns={"sizes"})
 * }
 *     )
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
     * @ORM\Cache("NONSTRICT_READ_WRITE", region="categories_region")
     * @ORM\OneToOne(targetEntity="Category", inversedBy="categoryConfigurations")
     */
    private $categoryId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $keyWords;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $negativeKeyWords;

    /**
     * @ORM\Column(type="jsonb", nullable=true)
     */
    private $sizes = [];

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
//        $this->keyWords = preg_replace('/\s+/', '', $keyWords);
        $this->keyWords = $keyWords;

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

    public function getNegativeKeyWords(): ?string
    {
        return $this->negativeKeyWords;
    }

    public function setNegativeKeyWords(string $negativeKeyWords): self
    {
//        $this->negativeKeyWords = preg_replace('/\s+/', '', $negativeKeyWords);;
        $this->negativeKeyWords = $negativeKeyWords;

        return $this;
    }

    public function getSizes()
    {
        return $this->sizes;
    }

    public function setSizes($sizes): self
    {
        $this->sizes = $sizes;

        return $this;
    }
}
