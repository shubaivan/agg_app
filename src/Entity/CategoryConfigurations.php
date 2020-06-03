<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryConfigurationsRepository")
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
     * @ORM\Column(type="string", length=255)
     */
    private $customCategoryName;

    /**
     * @ORM\Column(type="text")
     */
    private $keyWords;

    /**
     * @ORM\Column(type="text")
     */
    private $subKeyWords;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomCategoryName(): ?string
    {
        return $this->customCategoryName;
    }

    public function setCustomCategoryName(string $customCategoryName): self
    {
        $this->customCategoryName = $customCategoryName;

        return $this;
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

    public function getSubKeyWords(): ?string
    {
        return $this->subKeyWords;
    }

    public function setSubKeyWords(string $subKeyWords): self
    {
        $this->subKeyWords = preg_replace('/\s+/', '', $subKeyWords);;

        return $this;
    }
}
