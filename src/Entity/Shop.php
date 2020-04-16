<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation;
use App\Entity\Product;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShopRepository")
 * @ORM\Table(name="shop",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="shop_name_idx",
 *            columns={"name"})
 *    }
 * )
 * @UniqueEntity(fields={"name"})
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="entity_that_rarely_changes")
 */
class Shop
{
    const PREFIX_PROCESSING_DATA_SHOP_SUCCESSFUL = 'shop:processing:successful:';
    const PREFIX_PROCESSING_DATA_SHOP_FAILED = 'shop:processing:failed:';

    const PREFIX_HANDLE_DATA_SHOP_SUCCESSFUL = 'shop:handle:successful:';
    const PREFIX_HANDLE_DATA_SHOP_FAILED = 'shop:handle:failed:';

    const SERIALIZED_GROUP_LIST = 'shop_group_list';

    public static function getPrefixes() {
        return [
            'handle' => [
                'FAILED' => self::PREFIX_HANDLE_DATA_SHOP_FAILED,
                'SUCCESSFUL' => self::PREFIX_HANDLE_DATA_SHOP_SUCCESSFUL,
            ],
            'processing' => [
                'FAILED' => self::PREFIX_PROCESSING_DATA_SHOP_FAILED,
                'SUCCESSFUL' => self::PREFIX_PROCESSING_DATA_SHOP_SUCCESSFUL,
            ]
        ];
    }

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
    private $name;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
