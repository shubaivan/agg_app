<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="products",indexes={@ORM\Index(name="sku_idx", columns={"sku"})})
 */
class Product
{
    const SERIALIZED_GROUP_CREATE = 'group_create';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     * @Annotation\SerializedName("sKU")
     */
    private $sku;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     */
    private $shipping;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     */
    private $currency;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     */
    private $instock;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     * @Assert\Url()
     */
    private $productUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     * @Assert\Url()
     */
    private $imageUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     * @Assert\Url()
     */
    private $trackingUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     */
    private $originalPrice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     */
    private $ean;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     */
    private $manufacturerArticleNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     */
    private $extras;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param mixed $sku
     * @return Product
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     * @return Product
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @param mixed $shipping
     * @return Product
     */
    public function setShipping($shipping)
    {
        $this->shipping = $shipping;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     * @return Product
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInstock()
    {
        return $this->instock;
    }

    /**
     * @param mixed $instock
     * @return Product
     */
    public function setInstock($instock)
    {
        $this->instock = $instock;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductUrl()
    {
        return $this->productUrl;
    }

    /**
     * @param mixed $productUrl
     * @return Product
     */
    public function setProductUrl($productUrl)
    {
        $this->productUrl = $productUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param mixed $imageUrl
     * @return Product
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTrackingUrl()
    {
        return $this->trackingUrl;
    }

    /**
     * @param mixed $trackingUrl
     * @return Product
     */
    public function setTrackingUrl($trackingUrl)
    {
        $this->trackingUrl = $trackingUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param mixed $brand
     * @return Product
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOriginalPrice()
    {
        return $this->originalPrice;
    }

    /**
     * @param mixed $originalPrice
     * @return Product
     */
    public function setOriginalPrice($originalPrice)
    {
        $this->originalPrice = $originalPrice;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * @param mixed $ean
     * @return Product
     */
    public function setEan($ean)
    {
        $this->ean = $ean;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getManufacturerArticleNumber()
    {
        return $this->manufacturerArticleNumber;
    }

    /**
     * @param mixed $manufacturerArticleNumber
     * @return Product
     */
    public function setManufacturerArticleNumber($manufacturerArticleNumber)
    {
        $this->manufacturerArticleNumber = $manufacturerArticleNumber;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * @param mixed $extras
     * @return Product
     */
    public function setExtras($extras)
    {
        $this->extras = $extras;
        return $this;
    }
}
