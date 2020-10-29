<?php

namespace App\Entity;

use App\Exception\EntityValidatorException;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BrandShopRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="brands_region")
 *
 * @ORM\Table(name="brand_shop",
 *    uniqueConstraints={
 *        @UniqueConstraint(
 *          name="brand_shop_uniq_idx",
 *          columns={"brand_id", "shop_id"}
 *     ),
 *        @UniqueConstraint(
 *          name="brand_shop_uniq_slugs",
 *          columns={"brand_slug", "shop_slug"}
 *     ),
 *    }
 * )
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"brand", "shop"})
 * @UniqueEntity(fields={"brandSlug", "shopSlug"})
 */
class BrandShop implements EntityValidatorException
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
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $brandSlug;

    /**
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="brandShopRelation", fetch="LAZY")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotBlank()
     */
    private $brand;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $shopSlug;

    /**
     * @ORM\ManyToOne(targetEntity="Shop", inversedBy="shopBrandRelation", fetch="LAZY")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotBlank()
     */
    private $shop;

    public function getIdentity()
    {
        return $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;
        if ($brand instanceof Brand && $brand->getSlug()) {
            $this->brandSlug = $brand->getSlug();
        }
        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shop): self
    {
        $this->shop = $shop;
        if ($shop instanceof Shop && $shop->getSlug()) {
            $this->shopSlug = $shop->getSlug();
        }
        return $this;
    }
}
