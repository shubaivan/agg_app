<?php

namespace App\Entity;

use App\Exception\EntityValidatorException;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validation\Constraints\BrandStrategyRequiredArgs;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BrandStrategyRepository")
 * @ORM\Table(name="brand_strategy")
 * @BrandStrategyRequiredArgs()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="brands_region")
 */
class BrandStrategy implements EntityValidatorException
{
    use TimestampableEntity;

    const SERIALIZED_GROUP_BY_RELATION = 'brand_strategy_by_relation';

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
     *     Strategies::SERIALIZED_GROUP_GET_BY_SLUG,
     *     Brand::SERIALIZED_GROUP_BY_SLUG
     * })
     */
    private $requiredArgs = [];

    /**
     * @ORM\OneToOne(targetEntity="Brand", inversedBy="brandStrategies", fetch="LAZY")
     * @Assert\NotBlank()
     */
    private $brand;

    /**
     * @ORM\ManyToOne(targetEntity="Strategies", inversedBy="brandStrategies", fetch="LAZY")
     * @Assert\NotBlank()
     * @Annotation\Groups({
     *     Brand::SERIALIZED_GROUP_BY_SLUG,
     *     BrandStrategy::SERIALIZED_GROUP_BY_RELATION
     * })
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

    public function getIdentity()
    {
        return $this->getId();
    }
}
