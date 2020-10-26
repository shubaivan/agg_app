<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="files")
 * @ORM\Entity(repositoryClass="App\Repository\FilesRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="entity_that_rarely_changes")
 */
class Files
{
    use TimestampableEntity;

    const GROUP_GET = 'get_files';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({Files::GROUP_GET})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\File(
     *     maxSize = "100M"
     *     )
     * @Annotation\Groups({Files::GROUP_GET,
     *     Category::SERIALIZED_GROUP_RELATIONS_LIST,
     *     Shop::SERIALIZED_GROUP_GET_BY_SLUG
     *     })
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Annotation\Groups({Files::GROUP_GET})
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Annotation\Groups({Files::GROUP_GET})
     */
    private $originalName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Annotation\Groups({Files::GROUP_GET})
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Annotation\Groups({Files::GROUP_GET})
     */
    private $size;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @Annotation\Groups({Files::GROUP_GET})
     */
    private $enableShowFile = false;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="files")
     */
    private $category;

    /**
     * @var Brand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Brand", inversedBy="files")
     */
    private $brand;

    /**
     * @var Shop
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Shop", inversedBy="files")
     */
    private $shop;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({Files::GROUP_GET, Category::SERIALIZED_GROUP_RELATIONS_LIST})
     */
    private $description;

    /**
     * @var SlugInterface
     */
    private $bufferEntity;

    public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2).' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes.' bytes';
        } elseif (1 === $bytes) {
            $bytes = $bytes.' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path): self
    {
        if ($path instanceof UploadedFile) {
            $this->path = $path;
        } else {
            $commonPath = 'https://ams3.digitaloceanspaces.com/minimoj-consumer/minimoj-consumer/minimoj/';
            $commonPath .= $path;
            $this->path = $commonPath;
        }

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $this->formatSizeUnits($size);

        return $this;
    }

    public function getEnableShowFile(): ?bool
    {
        return $this->enableShowFile;
    }

    public function setEnableShowFile(?bool $enableShowFile): self
    {
        $this->enableShowFile = $enableShowFile;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return SlugInterface
     */
    public function getBufferEntity()
    {
        return $this->bufferEntity;
    }

    /**
     * @param $bufferEntity
     * @return $this
     */
    public function setBufferEntity($bufferEntity)
    {
        $this->bufferEntity = $bufferEntity;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shop): self
    {
        $this->shop = $shop;

        return $this;
    }
}
