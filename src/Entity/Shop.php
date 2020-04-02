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
 */
class Shop
{
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
    private $name;

    /**
     * @var Collection|Product[]
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
