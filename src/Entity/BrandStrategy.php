<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BrandStrategyRepository")
 * @ORM\Table(name="brand_strategy")
 */
class BrandStrategy
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Annotation\Groups({
     *     Strategies::SERIALIZED_GROUP_GET_BY_SLUG
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="jsonb", nullable=true)
     * @Annotation\Type("array")
     * @Annotation\Groups({
     *     Strategies::SERIALIZED_GROUP_GET_BY_SLUG
     * })
     */
    private $requiredArgs = [];

    /**
     * @ORM\OneToOne(targetEntity="Brand", inversedBy="brandStrategies", fetch="LAZY")
     */
    private $brand;

    /**
     * @ORM\OneToOne(targetEntity="Strategies", inversedBy="brandStrategies", fetch="LAZY")
     */
    private $strategy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequiredArgs()
    {
        return $this->requiredArgs;
    }

    public function setRequiredArgs($requiredArgs): self
    {
        $this->requiredArgs = $requiredArgs;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getStrategy(): ?Strategies
    {
        return $this->strategy;
    }

    public function setStrategy(?Strategies $strategy): self
    {
        $this->strategy = $strategy;

        return $this;
    }
}
