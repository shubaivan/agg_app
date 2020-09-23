<?php

namespace App\Entity;

use App\Exception\EntityValidatorException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
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
 *    },
 *     indexes={
 *     @ORM\Index(name="position_desc_index", columns={"position"}),
 *     @ORM\Index(name="position_asc_index", columns={"position"}),
 *     @ORM\Index(name="category_slug_index", columns={"slug"})
 * }
 * )
 * @UniqueEntity(fields={"categoryName"}, groups={Category::SERIALIZED_GROUP_CREATE})
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="entity_that_rarely_changes")
 * @ORM\HasLifecycleCallbacks()
 */
class Category extends SlugAbstract implements EntityValidatorException
{
    const SERIALIZED_GROUP_LIST = 'category_group_list';
    const SERIALIZED_GROUP_RELATIONS_LIST = 'category_group_relations_list';
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
     * @Annotation\Groups({Category::SERIALIZED_GROUP_LIST, Product::SERIALIZED_GROUP_LIST, Category::SERIALIZED_GROUP_RELATIONS_LIST})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Annotation\Groups({Category::SERIALIZED_GROUP_LIST, Category::SERIALIZED_GROUP_RELATIONS_LIST})
     * @Assert\NotBlank(groups={Category::SERIALIZED_GROUP_CREATE})
     * @Annotation\Accessor(getter="getCategoryNameAccessor", setter="setCategoryNameAccessor")
     */
    private $categoryName;

    /**
     * @var Collection|Product[]
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     * @ORM\ManyToMany(
     *     targetEntity="Product",
     *      mappedBy="categoryRelation",
     *      fetch="EXTRA_LAZY"
     * )
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="CategoryRelations", mappedBy="subCategory")
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     */
    private $subCategoryRelations;

    /**
     * @ORM\OneToMany(targetEntity="CategoryRelations", mappedBy="mainCategory")
     * @Annotation\Groups({Category::SERIALIZED_GROUP_RELATIONS_LIST})
     * @Annotation\Accessor(getter="getAccessorMainCategoryRelations")
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     */
    private $mainCategoryRelations;

    /**
     * @var CategoryConfigurations
     * @ORM\OneToOne(targetEntity="CategoryConfigurations", mappedBy="categoryId", cascade={"persist"})
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     */
    private $categoryConfigurations;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default": "0"})
     */
    private $customeCategory = false;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default": "0"})
     */
    private $hotCategory = false;

    /**
     * @var CategorySection
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     * @ORM\ManyToOne(targetEntity="CategorySection", inversedBy="categories", cascade={"persist"})
     * @Annotation\Groups({Category::SERIALIZED_GROUP_RELATIONS_LIST})
     */
    private $sectionRelation;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true, options={"default": "0"})
     * @Annotation\Groups({Category::SERIALIZED_GROUP_RELATIONS_LIST})
     */
    private $position;
    
    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->subCategoryRelations = new ArrayCollection();
        $this->mainCategoryRelations = new ArrayCollection();
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
        if ($categoryName) {
//            if ($categoryName == 'Sneakers'
//                || $categoryName == 'Gummistövlar'
//                || $categoryName == 'Tofflor & Sandaler'
//                || $categoryName == 'Babylek'
//                || $categoryName == 'T-shirts'
//                || $categoryName == 'Regnjackor'
//            ) {
//                $this->hotCategory = true;
//            }
            $this->categoryName = trim($categoryName);;
        }

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

    public function getCustomeCategory(): ?bool
    {
        return $this->customeCategory;
    }

    public function setCustomeCategory(?bool $customeCategory): self
    {
        $this->customeCategory = $customeCategory;

        return $this;
    }

    /**
     * @return Collection|CategoryRelations[]
     */
    public function getSubCategoryRelations(): Collection
    {
        return $this->subCategoryRelations;
    }

    public function addSubCategoryRelation(CategoryRelations $subCategoryRelation): self
    {
        if (!$this->subCategoryRelations->contains($subCategoryRelation)) {
            $this->subCategoryRelations[] = $subCategoryRelation;
            $subCategoryRelation->setSubCategory($this);
        }

        return $this;
    }

    public function removeSubCategoryRelation(CategoryRelations $subCategoryRelation): self
    {
        if ($this->subCategoryRelations->contains($subCategoryRelation)) {
            $this->subCategoryRelations->removeElement($subCategoryRelation);
            // set the owning side to null (unless already changed)
            if ($subCategoryRelation->getSubCategory() === $this) {
                $subCategoryRelation->setSubCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CategoryRelations[]
     */
    public function getMainCategoryRelations(): Collection
    {
        if (!$this->mainCategoryRelations) {
            $this->mainCategoryRelations = new ArrayCollection();
        }

        return $this->mainCategoryRelations;
    }

    public function addMainCategoryRelation(CategoryRelations $mainCategoryRelation): self
    {
        if (!$this->getMainCategoryRelations()->contains($mainCategoryRelation)) {
            $this->getMainCategoryRelations()->add($mainCategoryRelation);
            $mainCategoryRelation->setMainCategory($this);
        }

        return $this;
    }

    public function removeMainCategoryRelation(CategoryRelations $mainCategoryRelation): self
    {
        if ($this->getMainCategoryRelations()->contains($mainCategoryRelation)) {
            $this->getMainCategoryRelations()->removeElement($mainCategoryRelation);
            // set the owning side to null (unless already changed)
            if ($mainCategoryRelation->getMainCategory() === $this) {
                $mainCategoryRelation->setMainCategory(null);
            }
        }

        return $this;
    }

    public function getCategoryConfigurations(): ?CategoryConfigurations
    {
        return $this->categoryConfigurations;
    }

    public function setCategoryConfigurations(?CategoryConfigurations $categoryConfigurations): self
    {
        $this->categoryConfigurations = $categoryConfigurations;

        // set (or unset) the owning side of the relation if necessary
        $newCategoryId = null === $categoryConfigurations ? null : $this;
        if ($categoryConfigurations->getCategoryId() !== $newCategoryId) {
            $categoryConfigurations->setCategoryId($newCategoryId);
        }

        return $this;
    }

    public function getSectionRelation(): ?CategorySection
    {
        return $this->sectionRelation;
    }

    public function setSectionRelation(?CategorySection $sectionRelation): self
    {
        $this->sectionRelation = $sectionRelation;

        return $this;
    }

    public function getCategoryNameAccessor()
    {
        $categoryName = $this->categoryName;
        if (str_word_count($categoryName) > 1) {
            $categoryName = preg_replace('/\bsub\b/u', '', $this->categoryName);
            $categoryName = preg_replace('/\bBaby\b/u', '', $categoryName);
        }
        
        return trim($categoryName);
    }

    public function setCategoryNameAccessor(?string $categoryName = null)
    {
        $this->setCategoryName($categoryName);
    }

    /**
     * @param bool $hotCategory
     */
    public function setHotCategory(bool $hotCategory): void
    {
        $this->hotCategory = $hotCategory;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position ?? 0;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return CategoryRelations[]|ArrayCollection|Collection
     */
    public function getAccessorMainCategoryRelations()
    {
        $cond = Criteria::ASC;
        if (isset($_GET["sort_order"])) {
            $cond = $_GET["sort_order"];
        }
        $collection = $this->getMainCategoryRelations();
        $iterator = $collection->getIterator();
        $iterator->uasort(function ($a, $b) use ($cond) {
            /**
             * @var $a CategoryRelations
             * @var $b CategoryRelations
             */
            return ($a->getSubCategory()->getPosition() < $b->getSubCategory()->getPosition())
                ? ($cond === Criteria::DESC ? 1 : -1) : ($cond === Criteria::DESC ? -1 : 1);
        });
        $collection = new ArrayCollection(iterator_to_array($iterator));

        return $collection;
    }

    public function getDataFroSlug()
    {
        return $this->categoryName;
    }
}
