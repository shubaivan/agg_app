<?php

namespace App\Entity;

use App\Exception\EntityValidatorException;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ManuallyResourceJobRepository")
 *
 * @ORM\Table(name="manually_resource_job",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="redis_uniq_key_shop_uniq_idx",
 *            columns={"redis_uniq_key", "shop_key"})
 *    },
 *     indexes={
 *     @ORM\Index(name="shop_key_idx", columns={"shop_key"}),
 *     @ORM\Index(name="status_key_idx", columns={"status"})
 * }
 *     )
 * @UniqueEntity(fields={"redisUniqKey", "shopKey"})
 * @ORM\HasLifecycleCallbacks()
 */
class ManuallyResourceJob implements EntityValidatorException
{
    const STATUS_IN_PROGRESS = 1;
    const STATUS_FINISHED = 2;
    const STATUS_CREATED = 0;

    const FINISHED = 'finished';
    const IN_PROGRESS = 'in_progress';
    const CREATED = 'created';

    private static $enumStatus = [
        self::STATUS_IN_PROGRESS => self::IN_PROGRESS,
        self::STATUS_FINISHED => self::FINISHED,
        self::STATUS_CREATED => self::CREATED
    ];

    use TimestampableEntity;

    public function getIdentity()
    {
        return $this->getId();
    }


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"manually"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"manually"})
     */
    private $shopKey;

    /**
     * @ORM\Column(type="text")
     * @Groups({"manually"})
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"manually"})
     */
    private $dirForFiles;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"manually"})
     */
    private $redisUniqKey;

    /**
     * @ORM\Column(type="integer", options={"default": "0"})
     * @Groups({"manually"})
     */
    private $status = self::STATUS_CREATED;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="resourceJobs", cascade={"persist"})
     * @Annotation\MaxDepth(1)
     */
    private $createdAtAdmin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShopKey(): ?string
    {
        return $this->shopKey;
    }

    public function setShopKey(string $shopKey): self
    {
        $this->shopKey = $shopKey;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getDirForFiles(): ?string
    {
        return $this->dirForFiles;
    }

    public function setDirForFiles(string $dirForFiles): self
    {
        $this->dirForFiles = $dirForFiles;

        return $this;
    }

    public function getRedisUniqKey(): ?string
    {
        return $this->redisUniqKey;
    }

    public function setRedisUniqKey(string $redisUniqKey): self
    {
        $this->redisUniqKey = $redisUniqKey;

        return $this;
    }

    public function getCreatedAtAdmin(): ?User
    {
        return $this->createdAtAdmin;
    }

    public function setCreatedAtAdmin(?User $createdAtAdmin): self
    {
        $this->createdAtAdmin = $createdAtAdmin;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("enumStatusPresent")
     * @Annotation\Type("string")
     */
    public function getStoreNamesValue()
    {
        if (array_key_exists($this->status, self::$enumStatus)) {
            return self::$enumStatus[$this->status];
        }
        return '';
    }
}
