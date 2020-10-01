<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;
use App\Entity\Collection\Search\SearchProductCollection;
use App\Entity\Collection\SearchProducts\AdjacentProduct;

abstract class SEOModel extends SlugAbstract
{
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
}