<?php

namespace App\Entity\Collection\Search;

use App\Entity\Product;
use Doctrine\DBAL\Types\ConversionException;
use JMS\Serializer\Annotation;

class SearchProductCollection
{
    /**
     * @var array
     * @Annotation\Type("array")
     * @Annotation\Accessor(getter="getAccessorCollection")
     */
    private $collection;

    /**
     * @var int
     * @Annotation\Type("int")
     */
    private $count;

    /**
     * @var string
     * @Annotation\Type("string")
     */
    private $uniqIdentificationQuery;

    /**
     * SearchProductCollection constructor.
     * @param array $collection
     * @param int $count
     * @param string $uniqIdentificationQuery
     */
    public function __construct(
        array $collection,
        int $count,
        string $uniqIdentificationQuery = null)
    {
        $this->collection = $collection;
        $this->count = $count;
        $this->uniqIdentificationQuery = $uniqIdentificationQuery;
    }


    public function getAccessorCollection()
    {
        return $this->getCollection();
    }

    /**
     * @return array
     */
    public function getCollection(): array
    {
        $array_map = array_map(function ($key) {
            if (isset($key['extras']) && !is_array($key['extras'])) {
                $val = json_decode($key['extras'], true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw ConversionException::conversionFailed($key['extras'], $key['sku']);
                }
                $key['extras'] = $val;
            }

            return $key;
        }, $this->collection);

        return $array_map;
    }

    /**
     * @param array $collection
     * @return SearchProductCollection
     */
    public function setCollection(array $collection): SearchProductCollection
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return SearchProductCollection
     */
    public function setCount(int $count): SearchProductCollection
    {
        $this->count = $count;
        return $this;
    }
}