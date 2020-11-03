<?php

namespace App\Entity;

use App\Exception\EntityValidatorException;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validation\Constraints\BrandStrategyRequiredArgs;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BrandStrategyRepository")
 *
 * @ORM\Table(name="brand_strategy",
 *     uniqueConstraints={@UniqueConstraint(name="uniq_sbs_index",
 *      columns={"shop_id", "brand_id"})}
 *     )
 * @BrandStrategyRequiredArgs()
 * @UniqueEntity(fields={"brand", "shop"})
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
     * @ORM\Column(type="jsonb", nullable=false)
     * @Annotation\Type("array")
     * @Annotation\Groups({
     *     Strategies::SERIALIZED_GROUP_GET_BY_SLUG,
     *     Brand::SERIALIZED_GROUP_BY_SLUG
     * })
     */
    private $requiredArgs = [];

    /**
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="brandStrategies", fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $brand;

    /**
     * @ORM\ManyToOne(targetEntity="Strategies", inversedBy="brandStrategies", fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     * @Annotation\Groups({
     *     Brand::SERIALIZED_GROUP_BY_SLUG,
     *     BrandStrategy::SERIALIZED_GROUP_BY_RELATION
     * })
     */
    private $strategy;

    /**
     * @ORM\ManyToOne(targetEntity="Shop", inversedBy="brandStrategies", fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     * @Annotation\Groups({
     *     Brand::SERIALIZED_GROUP_BY_SLUG,
     *     BrandStrategy::SERIALIZED_GROUP_BY_RELATION
     * })
     */
    private $shop;

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

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shop): self
    {
        $this->shop = $shop;

        return $this;
    }
}
