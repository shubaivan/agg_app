<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShopRepository")
 * @ORM\Table(name="shop",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="shop_name_idx",
 *            columns={"shop_name"})
 *    }
 * )
 * @UniqueEntity(fields={"shopName"})
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="entity_that_rarely_changes")
 */
class Shop
{
    const PREFIX_HASH = 'statistic:';

    const PREFIX_HANDLE_ANALYSIS_PRODUCT_SUCCESSFUL = 'shop:handle:analysis_product_successful:';
    const PREFIX_PROCESSING_DATA_SHOP_SUCCESSFUL = 'shop:processing:successful:';
    const PREFIX_PROCESSING_DATA_SHOP_FAILED = 'shop:processing:failed:';
    const PREFIX_PROCESSING_DATA_SHOP_GLOBAL_MATCH_EXCEPTION_BRAND = 'shop:processing:global_match_exception_brand:';
    const PREFIX_PROCESSING_DATA_SHOP_GLOBAL_MATCH_EXCEPTION = 'shop:processing:global_match_exception:';
    const PREFIX_PROCESSING_DATA_SHOP_ADMIN_SHOP_RULES_EXCEPTION = 'shop:processing:admin_shop_rules_exception:';

    const PREFIX_HANDLE_DATA_SHOP_SUCCESSFUL = 'shop:handle:successful:';
    const PREFIX_HANDLE_DATA_SHOP_FAILED = 'shop:handle:failed:';

    const SERIALIZED_GROUP_LIST = 'shop_group_list';

    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Annotation\Groups({Shop::SERIALIZED_GROUP_LIST, Product::SERIALIZED_GROUP_LIST})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Annotation\Groups({Shop::SERIALIZED_GROUP_LIST})
     */
    private $shopName;

    /**
     * @var Collection|Product[]
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     * @ORM\OneToMany(targetEntity="Product", mappedBy="shopRelation", fetch="LAZY")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShopName(): ?string
    {
        return $this->shopName;
    }

    public function setShopName(string $shopName): self
    {
        $this->shopName = $shopName;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        if (!$this->products) {
            $this->products = new ArrayCollection();
        }

        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->getProducts()->contains($product)) {
            $this->products[] = $product;
            $product->setShopRelation($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->getProducts()->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getShopRelation() === $this) {
                $product->setShopRelation(null);
            }
        }

        return $this;
    }


}
