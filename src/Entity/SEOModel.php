<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;
use App\Entity\Collection\Search\SearchProductCollection;
use App\Entity\Collection\SearchProducts\AdjacentProduct;

abstract class SEOModel extends SlugAbstract
{
    protected static $templateTitleId = '';
    protected static $templateDescriptionId = '';

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
     *     Brand::SERIALIZED_GROUP_LIST_TH,
     *     Shop::SERIALIZED_GROUP_LIST,
     *     AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     Product::SERIALIZED_GROUP_LIST,
     *     Shop::SERIALIZED_GROUP_GET_BY_SLUG
     *     })
     */
    protected $seoTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Annotation\Accessor(getter="getSeoDescriptionAccessor")
     * @Annotation\Type("string")
     * @Annotation\Groups({
     *     Category::SERIALIZED_GROUP_LIST,
     *     Product::SERIALIZED_GROUP_LIST,
     *     Category::SERIALIZED_GROUP_RELATIONS_LIST,
     *     SearchProductCollection::GROUP_GET,
     *     Brand::SERIALIZED_GROUP_LIST,
     *     Brand::SERIALIZED_GROUP_LIST_TH,
     *     Shop::SERIALIZED_GROUP_LIST,
     *     AdjacentProduct::GROUP_GENERATE_ADJACENT,
     *     Product::SERIALIZED_GROUP_LIST,
     *     Shop::SERIALIZED_GROUP_GET_BY_SLUG
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
     *     Product::SERIALIZED_GROUP_LIST,
     *     Shop::SERIALIZED_GROUP_GET_BY_SLUG
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
     *     Product::SERIALIZED_GROUP_LIST,
     *     Shop::SERIALIZED_GROUP_GET_BY_SLUG
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
        $templateId = static::getTemplateTitleId();
        return $this->substituteParam($templateId, $this->seoTitle);
    }

    /**
     * @return mixed
     */
    public function getSeoDescription()
    {
        return $this->seoDescription;
    }

    public function getSeoDescriptionAccessor()
    {
        $templateId = static::getTemplateDescriptionId();
        return $this->substituteParam($templateId, $this->seoDescription);
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
    public static function getTemplateTitleId(): string
    {
        return static::$templateTitleId;
    }

    /**
     * @return string
     */
    public static function getTemplateDescriptionId(): string
    {
        return static::$templateDescriptionId;
    }

    /**
     * @param string $templateId
     * @param string $column
     * @return string|string[]|null
     */
    private function substituteParam(string $templateId, ?string $column)
    {
        if ($templateId && !$column) {
            $template = getenv($templateId);
            $column = preg_replace('/{{ name }}/',
                $this->getNameForSeoDefaultTemplate(), $template);
        }
        return $column;
    }

    public static function getSeoRenderColumns(): array
    {
        return ['seoTitle', 'seoDescription'];
    }
}