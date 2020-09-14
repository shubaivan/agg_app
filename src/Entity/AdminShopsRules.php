<?php

namespace App\Entity;

use App\Document\DataTableInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation;

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
class AdminShopsRules implements DataTableInterface
{
    use TimestampableEntity;

    const GROUP_LIST_TH = 'admin_shop_rule_group_list_th';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Annotation\Groups({AdminShopsRules::GROUP_LIST_TH})
     */
    private $store;

    /**
     * @ORM\Column(type="jsonb", nullable=false)
     * @Annotation\Groups({AdminShopsRules::GROUP_LIST_TH})
     */
    private $columnsKeywords;

    /**
     * @return string
     */
    public static function availableActions(): string
    {
        return 'create, edit';
    }

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

    public static function getImageColumns(): array
    {
        return [];
    }

    public static function getLinkColumns(): array
    {
        return [];
    }

    public static function getSeparateFilterColumn(): array
    {
        return [];
    }

    public static function getShortPreviewText(): array
    {
        return [];
    }

    public static function convertToHtmColumns(): array
    {
        return [];
    }

    /**
     * @return int
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("quantityRules")
     * @Annotation\Type("integer")
     * @Annotation\Groups({AdminShopsRules::GROUP_LIST_TH})
     */
    public function getQuantityRulesValue()
    {
        return 0;
    }

    /**
     * @return string
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("Action")
     * @Annotation\Type("string")
     * @Annotation\Groups({AdminShopsRules::GROUP_LIST_TH})
     */
    public function getActionValue()
    {
        return self::availableActions();
    }
}
