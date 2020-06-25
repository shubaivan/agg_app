<?php

namespace App\Entity;

use App\Exception\EntityValidatorException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="products",
 *     uniqueConstraints={@UniqueConstraint(name="uniq_sku_index", columns={"sku"})},
 *     indexes={
 *     @ORM\Index(name="sku_idx", columns={"sku"}),
 *     @ORM\Index(name="group_identity", columns={"group_identity"}),
 *     @ORM\Index(name="created_desc_index", columns={"created_at"}),
 *     @ORM\Index(name="created_asc_index", columns={"created_at"}),
 *     @ORM\Index(name="price_desc_index", columns={"price"}),
 *     @ORM\Index(name="price_asc_index", columns={"price"}),
 *     @ORM\Index(name="products_extras_idx", columns={"extras"})
 * }
 *     )
 *
 * @Annotation\AccessorOrder("custom",
 *      custom = {
 *     "id", "sku", "name",
 *     "description", "category", "price",
 *     "shipping", "currency", "instock", "productUrl", "imageUrl",
 *     "trackingUrl", "brand", "originalPrice", "ean", "manufacturerArticleNumber",
 *     "extras", "createdAt"
 *      }
 * )
 * @ORM\Cache("NONSTRICT_READ_WRITE")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"sku"}, groups={Product::SERIALIZED_GROUP_CREATE})
 */
class Product implements EntityValidatorException
{
    use TimestampableEntity;

    const SERIALIZED_GROUP_CREATE = 'product_group_create';
    const SERIALIZED_GROUP_LIST = 'product_group_list';
    const SERIALIZED_GROUP_CREATE_IDENTITY = 'product_group_create_identity';
    const SIZE = 'SIZE';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(
     *     groups={Product::SERIALIZED_GROUP_CREATE}
     * )
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $sku;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank(
     *     groups={Product::SERIALIZED_GROUP_CREATE_IDENTITY}
     * )
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     */
    private $groupIdentity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *     groups={Product::SERIALIZED_GROUP_CREATE}
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *     groups={Product::SERIALIZED_GROUP_CREATE}
     * )
     */
    private $category;

    /**
     * @var Collection|Category[]
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="products", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     */
    private $categoryRelation;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *     groups={Product::SERIALIZED_GROUP_CREATE}
     * )
     */
    private $shipping;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *     groups={Product::SERIALIZED_GROUP_CREATE}
     * )
     */
    private $currency;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *     groups={Product::SERIALIZED_GROUP_CREATE}
     * )
     */
    private $instock;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     * @Assert\Url(
     *     groups={Product::SERIALIZED_GROUP_CREATE}
     * )
     */
    private $productUrl;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     * @Assert\Url(
     *     groups={Product::SERIALIZED_GROUP_CREATE}
     * )
     */
    private $imageUrl;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     * @Assert\Url(
     *     groups={Product::SERIALIZED_GROUP_CREATE}
     * )
     */
    private $trackingUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *     groups={Product::SERIALIZED_GROUP_CREATE}
     * )
     */
    private $brand;

    /**
     * @var Brand
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="products", cascade={"persist"})
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     */
    private $brandRelation;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $originalPrice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *     groups={Product::SERIALIZED_GROUP_CREATE}
     * )
     */
    private $ean;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *     groups={Product::SERIALIZED_GROUP_CREATE}
     * )
     */
    private $manufacturerArticleNumber;

    /**
     * @ORM\Column(type="jsonb", nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE})
     * @Annotation\Accessor(setter="setExtrasAccessor")
     * @Annotation\Type("string")
     */
    private $extras;

    /**
     * @var Collection|UserIpProduct[]
     * @ORM\OneToMany(targetEntity="UserIpProduct", mappedBy="products")
     */
    private $userIpProducts;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Annotation\Groups({Product::SERIALIZED_GROUP_CREATE, Product::SERIALIZED_GROUP_LIST})
     */
    private $shop;

    /**
     * @var Shop
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     * @ORM\ManyToOne(targetEntity="Shop", inversedBy="products", cascade={"persist"})
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     */
    private $shopRelation;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default": "0"})
     */
    private $matchForCategories = false;

    public function __construct()
    {
        $this->userIps = new ArrayCollection();
        $this->categoryRelation = new ArrayCollection();
        $this->shopRelation = new ArrayCollection();
        $this->userIpProducts = new ArrayCollection();
    }

    /**
     * @return \DateTime
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("createdAt")
     * @Annotation\Type("DateTime<'Y:m:d H:i:s'>")
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     */
    public function getCreatedAtValue()
    {
        return $this->createdAt;
    }

    /**
     * @return array
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("extraFields")
     * @Annotation\Type("array")
     * @Annotation\Groups({Product::SERIALIZED_GROUP_LIST})
     */
    public function getExtraFields()
    {
        return $this->getExtras();
    }

    /**
     * @return int|null
     */
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
     * @return string
     */
    public function getIdentity()
    {
        return 'shop: ' . $this->getShop() . ' sku: ' . $this->getSku();
    }

    public function getBrandRelation(): ?Brand
    {
        return $this->brandRelation;
    }

    public function setBrandRelation(?Brand $brandRelation): self
    {
        $this->brandRelation = $brandRelation;
        $this->setBrand($brandRelation->getBrandName());

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return array
     */
    public function getCategoriesNameArray()
    {
        $collection = $this->getCategoryRelation()->map(function (Category $category) {
            return $category->getCategoryName();
        });

        return $collection->count() ? $collection->toArray() : [];
    }

    public function getShop(): ?string
    {
        return $this->shop;
    }

    public function setShop(?string $shop): self
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategoryRelation(): Collection
    {
        if (!$this->categoryRelation) {
            $this->categoryRelation = new ArrayCollection();
        }

        return $this->categoryRelation;
    }

    public function addCategoryRelation(Category $categoryRelation): self
    {
        if (!$this->getCategoryRelation()->contains($categoryRelation)) {
            $this->getCategoryRelation()->add($categoryRelation);
        }

        return $this;
    }

    public function removeCategoryRelation(Category $categoryRelation): self
    {
        if ($this->getCategoryRelation()->contains($categoryRelation)) {
            $this->categoryRelation->removeElement($categoryRelation);
        }

        return $this;
    }

    public function getShopRelation(): ?Shop
    {
        return $this->shopRelation;
    }

    public function setShopRelation(?Shop $shopRelation): self
    {
        $this->shopRelation = $shopRelation;
        $this->setShop($shopRelation->getShopName());

        return $this;
    }

    /**
     * @param string|null $extras
     */
    public function setExtrasAccessor(?string $extras)
    {
        if (is_null($extras)) {
            return;
        }
        $result = [];
        $preg_match_all = preg_match_all('~{([^{}]*)}~', $extras, $matches);
        if ($preg_match_all > 0) {
            if (isset($matches[1]) && is_array($matches[1])) {
                $extraFields = $matches[1];
                foreach ($extraFields as $field) {
                    if (preg_match_all('/[#]/', $field, $matches) > 0) {
                        $explode = explode('#', $field);
                        if (isset($explode[0]) && isset($explode[1])) {
                            if ($explode[0] == self::SIZE) {
                                if (preg_match_all('/[0-9]+/', $explode[1], $matchesD)) {
                                    $sizes = array_shift($matchesD);
                                    $result[$explode[0]] = $sizes;
                                } else {
                                    $result[$explode[0]] = [];
                                }
                                array_push($result[$explode[0]], $explode[1]);
                            } else {
                                $result[$explode[0]] = $explode[1];
                            }
                        }
                    }
                }
            }
        }

        $this->setExtras($result);
    }

    public function getExtras()
    {
        return $this->extras;
    }

    public function setExtras($extras): self
    {
        $this->extras = $extras;

        return $this;
    }

    public function setSeparateExtra($key, $value): self
    {
        $value = trim($value);
        $extras = $this->getExtras();

        if (is_array($extras)) {
            if ($key === self::SIZE) {
                if (isset($extras[$key]) && !is_array($extras[$key])) {
                    unset($extras[$key]);
                }
                $extras[$key][] = $value;
                $this->extras = $extras;
            } elseif(!array_key_exists($key, $extras)) {
                $this->extras = array_merge($extras, [$key => $value]);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserIpProduct[]
     */
    public function getUserIpProducts(): Collection
    {
        if (!$this->userIpProducts) {
            $this->userIpProducts = new ArrayCollection();
        }
        return $this->userIpProducts;
    }

    public function addUserIpProduct(UserIpProduct $userIpProduct): self
    {
        if (!$this->getUserIpProducts()->contains($userIpProduct)) {
            $this->userIpProducts[] = $userIpProduct;
            $userIpProduct->setProducts($this);
        }

        return $this;
    }

    public function removeUserIpProduct(UserIpProduct $userIpProduct): self
    {
        if ($this->getUserIpProducts()->contains($userIpProduct)) {
            $this->userIpProducts->removeElement($userIpProduct);
            // set the owning side to null (unless already changed)
            if ($userIpProduct->getProducts() === $this) {
                $userIpProduct->setProducts(null);
            }
        }

        return $this;
    }

    public function getGroupIdentity(): ?string
    {
        return $this->groupIdentity;
    }

    public function setGroupIdentity(?string $groupIdentity): self
    {
        $this->groupIdentity = $groupIdentity;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMatchForCategories(): bool
    {
        return $this->matchForCategories;
    }

    /**
     * @param bool $matchForCategories
     * @return Product
     */
    public function setMatchForCategories(bool $matchForCategories): Product
    {
        $this->matchForCategories = $matchForCategories;
        return $this;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->matchCategoriesNames();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->matchCategoriesNames();
    }

    private function matchCategoriesNames()
    {
        $categoryNames = $this->getCategoryRelation()->map(function (Category $category) {
            return $category->getCategoryName();
        });
        if ($categoryNames->count()) {
            $arrayCategoryNames = $categoryNames->toArray();
            $arrayCategoryNames = array_unique($arrayCategoryNames);
            $implode = implode(' - ', $arrayCategoryNames);
            $this->setCategory($implode);
        }
    }

    /**
     * @return string
     */
    public function getSearchDataForRelatedProductItems()
    {
        $pieces = [
            $this->getName(),
            $this->getPrice()
        ];

        if ($this->getBrandRelation()) {
            array_push($pieces, $this->getBrandRelation()->getBrandName());
        }

//        if ($this->getCategoryRelation()->count()) {
//            array_push($pieces, implode(',', $this->getCategoriesNameArray()));
//        }

        $search = implode(',', $pieces);
        $replace = preg_replace('/[^a-zA-Z0-9 ,.¤æøĂéëäöåÉÄÖÅ™]/', " ", $search);
        $matchData = preg_replace('!\s+!', ',', $replace);

        return $matchData;
    }
}
