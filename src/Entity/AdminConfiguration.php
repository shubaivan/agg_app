<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminConfigurationRepository")
 */
class AdminConfiguration
{
    const GLOBAL_NEGATIVE_KEY_WORDS = 'GLOBAL_NEGATIVE_KEY_WORDS';
    const GLOBAL_NEGATIVE_BRAND_KEY_WORDS = 'GLOBAL_NEGATIVE_BRAND_KEY_WORDS';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $propertyName;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $propertyData;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPropertyName(): ?string
    {
        return $this->propertyName;
    }

    public function setPropertyName(string $propertyName): self
    {
        $this->propertyName = $propertyName;

        return $this;
    }

    public function getPropertyData(): ?string
    {
        return $this->propertyData;
    }

    public function setPropertyData(string $propertyData): self
    {
        $this->propertyData = $propertyData;

        return $this;
    }
}
