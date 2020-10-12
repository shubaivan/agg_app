<?php

namespace App\Entity;

use App\Document\DataTableInterface;
use App\Exception\EntityValidatorException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BrandRepository")
 * @ORM\Table(name="brand",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="brand_name_idx",columns={"brand_name"}),
 *        @UniqueConstraint(name="brand_slug_idx", columns={"slug"})
 *    }
 * )
 * @UniqueEntity(fields={"brandName"}, groups={Brand::SERIALIZED_GROUP_CREATE})
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="brands_region")
 * @Annotation\AccessorOrder("custom", custom = {
 *     "id",
 *     "brandName"
 * })
 * @ORM\HasLifecycleCallbacks()
 */
class Brand extends SEOModel implements EntityValidatorException, DataTableInterface, AttachmentFilesInterface
{
    const SERIALIZED_GROUP_LIST = 'brand_group_list';
    const SERIALIZED_GROUP_LIST_TH = 'th_brand_group_list';
    const SERIALIZED_GROUP_CREATE = 'brand_group_crete';

    protected static $templateTitleId = 'BRAND_SEO_META_TITLE';
    protected static $templateDescriptionId = 'BRAND_SEO_META_DESCRIPTION';

    use TimestampableEntity;

    public function getIdentity()
    {
        return $this->getId();
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Annotation\Groups({Brand::SERIALIZED_GROUP_LIST,
     *      Product::SERIALIZED_GROUP_LIST, Brand::SERIALIZED_GROUP_LIST_TH})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Annotation\Groups({Brand::SERIALIZED_GROUP_LIST, Brand::SERIALIZED_GROUP_LIST_TH})
     * @Assert\NotBlank(groups={Brand::SERIALIZED_GROUP_CREATE})
     */
    private $brandName;

    /**
     * @var Product[]|Collection
     * @ORM\Cache("NONSTRICT_READ_WRITE", region="brands_region")
     * @ORM\OneToMany(targetEntity="Product", mappedBy="brandRelation",fetch="LAZY")
     */
    private $products;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default": "0"})
     * @Annotation\Groups({Brand::SERIALIZED_GROUP_LIST})
     */
    private $top = false;

    /**
     * @var Files
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Files",
     *     mappedBy="brand",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     *     )
     * @Assert\Valid()
     * @ORM\Cache("NONSTRICT_READ_WRITE", region="brands_region")
     */
    private $files;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrandName(): ?string
    {
        return $this->brandName;
    }

    public function setBrandName(string $brandName): self
    {
//        if ($brandName == 'Mini Rodini'
//            || $brandName == 'Adidas'
//            || $brandName == 'Lindex'
//            || $brandName == 'Reima'
//            || $brandName == 'Jack & Jones'
//            || $brandName == 'Polarn O. Pyret'
//        ) {
//            $this->top = true;
//        }
        $this->brandName = $brandName;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setBrandRelation($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getBrandRelation() === $this) {
                $product->setBrandRelation(null);
            }
        }

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
     * @Annotation\SerializedName("quantityProducts")
     * @Annotation\Type("integer")
     * @Annotation\Groups({Brand::SERIALIZED_GROUP_LIST_TH})
     */
    public function getQuantityProductsValue()
    {
        return 0;
    }

    /**
     * @return int
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("Action")
     * @Annotation\Type("string")
     * @Annotation\Groups({Brand::SERIALIZED_GROUP_LIST_TH})
     */
    public function getActionValue()
    {
        return 'edit';
    }

    /**
     * @param bool $top
     */
    public function setTop(bool $top): void
    {
        $this->top = $top;
    }

    public static function arrayColumns(): array
    {
        return [];
    }

    public function getDataFroSlug()
    {
        return $this->brandName;
    }

    public function getNameForSeoDefaultTemplate()
    {
        return $this->brandName;
    }

    /**
     * @return Collection|Files[]
     */
    public function getFiles(): Collection
    {
        if (!$this->files) {
            $this->files = new ArrayCollection();
        }
        return $this->files;
    }

    public function addFile(Files $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setBrand($this);
        }

        return $this;
    }

    public function removeFile(Files $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getBrand() === $this) {
                $file->setBrand(null);
            }
        }

        return $this;
    }

    public function checkFileExist($name)
    {
        $isCheck = false;
        $files = $this->getFiles()->getValues();
        foreach ($files as $file) {
            /** @var Files $file */
            $isCheck = ($file->getOriginalName() === $name);
            if ($isCheck) {
                break;
            }
        }

        return $isCheck;
    }
}
