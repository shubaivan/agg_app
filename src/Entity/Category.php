<?php

namespace App\Entity;

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
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @ORM\Table(name="category",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="category_name_idx",
 *            columns={"category_name"})
 *    }
 * )
 * @UniqueEntity(fields={"categoryName"}, groups={Category::SERIALIZED_GROUP_CREATE})
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="entity_that_rarely_changes")
 */
class Category implements EntityValidatorException
{
    const SERIALIZED_GROUP_LIST = 'category_group_list';
    const SERIALIZED_GROUP_CREATE = 'category_group_crete';

    public function getIdentity()
    {
        return $this->getId();
    }

    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Annotation\Groups({Category::SERIALIZED_GROUP_LIST, Product::SERIALIZED_GROUP_LIST})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Annotation\Groups({Category::SERIALIZED_GROUP_LIST})
     * @Assert\NotBlank(groups={Category::SERIALIZED_GROUP_CREATE})
     */
    private $categoryName;

    /**
     * @var Collection|Product[]
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="categoryRelation", fetch="EXTRA_LAZY")
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

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(string $categoryName): self
    {
        $this->categoryName = $categoryName;

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
            $product->addCategoryRelation($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->getProducts()->contains($product)) {
            $this->products->removeElement($product);
            $product->removeCategoryRelation($this);
        }

        return $this;
    }
}
