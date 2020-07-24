<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminShopsRulesRepository")
 *
 * @ORM\Table(name="admin_shops_rules",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="store_name_idx",
 *            columns={"store"})
 *    }
 * )
 * @UniqueEntity(fields={"store"})
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="entity_that_rarely_changes")
 */
class AdminShopsRules
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $store;

    /**
     * @ORM\Column(type="jsonb", nullable=false)
     */
    private $columnsKeywords;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStore(): ?string
    {
        return $this->store;
    }

    public function setStore(string $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getColumnsKeywords()
    {
        return $this->columnsKeywords;
    }

    public function setColumnsKeywords($columnsKeywords): self
    {
        $this->columnsKeywords = $columnsKeywords;

        return $this;
    }
}
