<?php

namespace App\EntityMysql;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;


/**
 * @ORM\Entity(repositoryClass="App\RepositoryMysql\ColoursRepository")
 * @ORM\Table(name="colours",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="original_color_idx",
 *            columns={"original_color"})
 *    }
 * )
 * @UniqueEntity(fields={"originalColor"})
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="entity_that_rarely_changes")
 */
class Colours
{
    use TimestampableEntity;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $originalColor;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $substituteColor;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginalColor(): ?string
    {
        return $this->originalColor;
    }

    public function setOriginalColor(string $originalColor): self
    {
        $this->originalColor = $originalColor;

        return $this;
    }

    public function getSubstituteColor(): ?string
    {
        return $this->substituteColor;
    }

    public function setSubstituteColor(string $substituteColor): self
    {
        $this->substituteColor = $substituteColor;

        return $this;
    }
}
