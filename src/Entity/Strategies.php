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

    const SERIALIZED_GROUP_GET_BY_SLUG = 'get_by_slug';

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
     * @ORM\Column(type="string")
     * @Annotation\Groups({
     *     Strategies::SERIALIZED_GROUP_GET_BY_SLUG
     * })
     */
    private $strategyName;

    /**
     * @ORM\Column(type="string")
     * @Annotation\Groups({
     *     Strategies::SERIALIZED_GROUP_GET_BY_SLUG
     * })
     */
    private $strategyNameSpace;

    /**
     * @ORM\Column(type="text")
     * @Annotation\Groups({
     *     Strategies::SERIALIZED_GROUP_GET_BY_SLUG
     * })
     */
    private $description;

    /**
     * @ORM\Column(type="jsonb", nullable=true)
     * @Annotation\Type("array")
     * @Annotation\Groups({
     *     Strategies::SERIALIZED_GROUP_GET_BY_SLUG
     * })
     */
    private $requiredInputs = [];

    /**
     * @ORM\Column(type="jsonb", nullable=true)
     * @Annotation\Type("array")
     * @Annotation\Groups({
     *     Strategies::SERIALIZED_GROUP_GET_BY_SLUG
     * })
     */
    private $requiredArgs = [];


    /**
     * @var BrandStrategy
     * @ORM\OneToOne(targetEntity="BrandStrategy",
     *      mappedBy="strategy",
     *      cascade={"persist"}
     *     )
     */
    private $brandStrategies;

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

    public function getRequiredArgs()
    {
        return $this->requiredArgs;
    }

    public function setRequiredArgs($requiredArgs): self
    {
        $this->requiredArgs = $requiredArgs;

        return $this;
    }

    public function getBrandStrategies(): ?BrandStrategy
    {
        return $this->brandStrategies;
    }

    public function setBrandStrategies(?BrandStrategy $brandStrategies): self
    {
        $this->brandStrategies = $brandStrategies;

        // set (or unset) the owning side of the relation if necessary
        $newStrategy = null === $brandStrategies ? null : $this;
        if ($brandStrategies->getStrategy() !== $newStrategy) {
            $brandStrategies->setStrategy($newStrategy);
        }

        return $this;
    }
}
