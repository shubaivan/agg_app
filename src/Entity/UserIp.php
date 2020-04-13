<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserIpRepository")
 */
class UserIp
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ip;

    /**
     * @var Collection|UserIpProduct[]
     * @ORM\OneToMany(targetEntity="UserIpProduct", mappedBy="ips")
     */
    private $userIpProducts;

    public function __construct()
    {
        $this->userIpProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return Collection|UserIpProduct[]
     */
    public function getUserIpProducts(): Collection
    {
        if (!$this->userIpProducts) {
            $this->userIpProducts = new ArrayCollection();
        }
        return $this->userIpProducts;
    }

    public function addUserIpProduct(UserIpProduct $userIpProduct): self
    {
        if (!$this->getUserIpProducts()->contains($userIpProduct)) {
            $this->userIpProducts[] = $userIpProduct;
            $userIpProduct->setIps($this);
        }

        return $this;
    }

    public function removeUserIpProduct(UserIpProduct $userIpProduct): self
    {
        if ($this->getUserIpProducts()->contains($userIpProduct)) {
            $this->userIpProducts->removeElement($userIpProduct);
            // set the owning side to null (unless already changed)
            if ($userIpProduct->getIps() === $this) {
                $userIpProduct->setIps(null);
            }
        }

        return $this;
    }
}
