<?php

namespace App\Entity\Collection;

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
        $array_map = array_map(function ($data) {
            foreach ($data as $key => $row) {
                $substr = substr($row, 1, -1); //explode(',', $substr)
                if ($key == 'extras' && !is_array($row)) {
                    if (preg_match_all('#\{(.*?)\}#', $substr, $match) > 1) {
                        $setExtra = array_shift($match);
                        $setExtraResult = [];
                        foreach ($setExtra as $partExtra) {
                            $partExtraArray = json_decode($partExtra, true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                throw ConversionException::conversionFailed($key['extras'], $key['allSku']);
                            }
                            $setExtraResult = array_merge_recursive($setExtraResult, $partExtraArray);
                        }
                        array_walk($setExtraResult, function (&$v) {
                            $v = array_unique($v);
                        });
                        $val = $setExtraResult;
                    } else {
                        $val = json_decode($substr, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw ConversionException::conversionFailed($key['extras'], $key['allSku']);
                        }
                    }
                } else {
                    if (preg_match_all('/\"([^\"]*?)\"/', $row, $commonMatches) > 0) {
                        $extractValue = array_shift($commonMatches);
                        array_walk($extractValue, function (&$v) {
                            $v = trim($v, '"');
                        });
                        if (count($extractValue) > 1) {
                            $val = $extractValue;
                        }
                    } else {
                        $val = trim($substr, '"');
                        if (count(explode(',', $val)) > 1) {
                            $val = explode(',', $val);
                        }
                    }
                }
                $data[$key] = $val;
            }
            return $data;
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