<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StrategiesRepository")
 * @ORM\Table(name="strategies",
 *    uniqueConstraints={
 *        @UniqueConstraint(
 *          name="strategy_slug_idx",
 *          columns={"slug"}
 *     )
 *    }
 * )
 */
class Strategies extends SlugAbstract
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $strategyName;

    /**
     * @ORM\Column(type="string")
     */
    private $strategyNameSpace;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="jsonb", nullable=true)
     * @Annotation\Type("array")
     */
    private $requiredInputs = [];

    /**
     * @var Collection|Brand[]
     * @ORM\OneToMany(targetEntity="Brand", mappedBy="strategy", fetch="LAZY")
     */
    private $brands;

    public function __construct()
    {
        $this->brands = new ArrayCollection();
    }

    public function getDataFroSlug()
    {
        return $this->strategyName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStrategyName(): ?string
    {
        return $this->strategyName;
    }

    public function setStrategyName(string $strategyName): self
    {
        $this->strategyName = $strategyName;

        return $this;
    }

    public function getStrategyNameSpace(): ?string
    {
        return $this->strategyNameSpace;
    }

    public function setStrategyNameSpace(string $strategyNameSpace): self
    {
        $this->strategyNameSpace = $strategyNameSpace;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRequiredInputs()
    {
        return $this->requiredInputs;
    }

    public function setRequiredInputs($requiredInputs): self
    {
        $this->requiredInputs = $requiredInputs;

        return $this;
    }

    /**
     * @return Collection|Brand[]
     */
    public function getBrands(): Collection
    {
        if (!$this->brands) {
            $this->brands = new ArrayCollection();
        }

        return $this->brands;
    }

    public function addBrand(Brand $brand): self
    {
        if (!$this->getBrands()->contains($brand)) {
            $this->getBrands()->add($brand);
            $brand->setStrategy($this);
        }

        return $this;
    }

    public function removeBrand(Brand $brand): self
    {
        if ($this->getBrands()->contains($brand)) {
            $this->getBrands()->removeElement($brand);
            // set the owning side to null (unless already changed)
            if ($brand->getStrategy() === $this) {
                $brand->setStrategy(null);
            }
        }

        return $this;
    }
}
