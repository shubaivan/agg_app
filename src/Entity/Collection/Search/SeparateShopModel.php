<?php


namespace App\Entity\Collection\Search;

use JMS\Serializer\Annotation;
use App\Entity\Collection\Search\SearchShopsCollection;

class SeparateShopModel
{
    /**
     * @var int
     * @Annotation\Type("int")
     * @Annotation\Groups({SearchShopsCollection::SERIALIZED_GROUP_LIST})
     */
    private $id;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchShopsCollection::SERIALIZED_GROUP_LIST})
     */
    private $shopName;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchShopsCollection::SERIALIZED_GROUP_LIST})
     */
    private $createdAt;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Groups({SearchShopsCollection::SERIALIZED_GROUP_LIST})
     */
    private $slug;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Accessor(setter="setCategoryIdsAccessor")
     */
    private $categoryIds;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\Accessor(setter="setFilePathAccessor")
     */
    private $filePath;

    /**
     * @var array
     * @Annotation\Groups({SearchShopsCollection::SERIALIZED_GROUP_LIST})
     */
    private $categoryModels = [];

    /**
     * @var array
     * @Annotation\Groups({SearchShopsCollection::SERIALIZED_GROUP_LIST})
     */
    private $fileModels = [];

    public function setCategoryIdsAccessor(string $ids)
    {
        $this->categoryIds = $ids;
        $this->categoryModels = $this->convertToArray($ids);
    }

    public function setFilePathAccessor(string $path)
    {
        $this->filePath = $path;
        $this->fileModels = $this->convertToArray($path);
    }

    /**
     * @param string $data
     * @return array|array[]|false|string[]
     */
    private function convertToArray(string $data)
    {
        $preg_replace = preg_replace('/\{|\}/', '', $data);
        if ($preg_replace) {
            $preg_split = preg_split('/,/', $preg_replace);
        }

        return $preg_split ?? [];
    }

    /**
     * @return array
     */
    public function getCategoryModels(): array
    {
        return $this->categoryModels;
    }

    /**
     * @param array $categoryModels
     */
    public function setCategoryModels(array $categoryModels): void
    {
        $this->categoryModels = $categoryModels;
    }

    /**
     * @return array
     */
    public function getFileModels(): array
    {
        return $this->fileModels;
    }

    /**
     * @param array $fileModels
     */
    public function setFileModels(array $fileModels): void
    {
        $this->fileModels = $fileModels;
    }
}