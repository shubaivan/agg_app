<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;
use App\Entity\Collection\Search\SearchProductCollection;
use App\Entity\Collection\SearchProducts\AdjacentProduct;

abstract class SEOModel extends SlugAbstract
{
    protected static $templateId = '';

    abstract public function getNameForSeoDefaultTemplate();

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Annotation\Accessor(getter="getSeoTitleAccessor")
     * @Annotation\Type("string")
     * @Annotation\Groups({
     *     Category::SERIALIZED_GROUP_LIST,
     *     Product::SERIALIZED_GROUP_LIST,
     *     Category::SERIALIZED_GROUP_RELATIONS_LIST,
     *     SearchProductCollection::GROUP_GET,
     *     Brand::SERIALIZED_GROUP_LIST,
     *     Shop::SERIALIZED_GROUP_LIST,
     *     AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     Product::SERIALIZED_GROUP_LIST
     *     })
     */
    protected $seoTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Annotation\Type("string")
     * @Annotation\Groups({
     *     Category::SERIALIZED_GROUP_LIST,
     *     Product::SERIALIZED_GROUP_LIST,
     *     Category::SERIALIZED_GROUP_RELATIONS_LIST,
     *     SearchProductCollection::GROUP_GET,
     *     Brand::SERIALIZED_GROUP_LIST,
     *     Shop::SERIALIZED_GROUP_LIST,
     *     AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     Product::SERIALIZED_GROUP_LIST
     *     })
     */
    protected $seoDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Annotation\Type("string")
     * @Annotation\Groups({
     *     Category::SERIALIZED_GROUP_LIST,
     *     Product::SERIALIZED_GROUP_LIST,
     *     Category::SERIALIZED_GROUP_RELATIONS_LIST,
     *     SearchProductCollection::GROUP_GET,
     *     Brand::SERIALIZED_GROUP_LIST,
     *     Shop::SERIALIZED_GROUP_LIST,
     *     AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     Product::SERIALIZED_GROUP_LIST
     *     })
     */
    protected $seoText1;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Annotation\Type("string")
     * @Annotation\Groups({
     *     Category::SERIALIZED_GROUP_LIST,
     *     Product::SERIALIZED_GROUP_LIST,
     *     Category::SERIALIZED_GROUP_RELATIONS_LIST,
     *     SearchProductCollection::GROUP_GET,
     *     Brand::SERIALIZED_GROUP_LIST,
     *     Shop::SERIALIZED_GROUP_LIST,
     *     AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     Product::SERIALIZED_GROUP_LIST
     *     })
     */
    protected $seoText2;

    /**
     * @param mixed $seoTitle
     */
    public function setSeoTitle($seoTitle): void
    {
        $this->seoTitle = $seoTitle;
    }

    /**
     * @param mixed $seoDescription
     */
    public function setSeoDescription($seoDescription): void
    {
        $this->seoDescription = $seoDescription;
    }

    /**
     * @param mixed $seoText1
     */
    public function setSeoText1($seoText1): void
    {
        $this->seoText1 = $seoText1;
    }

    /**
     * @param mixed $seoText2
     */
    public function setSeoText2($seoText2): void
    {
        $this->seoText2 = $seoText2;
    }

    public function getSeoTitleAccessor()
    {
        return $this->getSeoTitle();
    }

    /**
     * @return mixed
     */
    public function getSeoTitle()
    {
        $templateId = static::getTemplateId();
        $substituteName = $this->seoTitle;
        if ($templateId && !$this->seoTitle) {
            $seoTitle = getenv('CATEGORY_SEO_META_TITLE');
            $substituteName = preg_replace('/{{ name }}/',
                $this->getNameForSeoDefaultTemplate(), $seoTitle);
        }

        return $substituteName;
    }

    /**
     * @return mixed
     */
    public function getSeoDescription()
    {
        return $this->seoDescription;
    }

    /**
     * @return mixed
     */
    public function getSeoText1()
    {
        return $this->seoText1;
    }

    /**
     * @return mixed
     */
    public function getSeoText2()
    {
        return $this->seoText2;
    }

    /**
     * @return string
     */
    public static function getTemplateId(): string
    {
        return static::$templateId;
    }
}