<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserIpProductRepository")
 */
class UserIpProduct
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="userIpProducts", cascade={"persist"})
     */
    private $products;

    /**
     * @var UserIp
     * @ORM\ManyToOne(targetEntity="UserIp", inversedBy="userIpProducts", cascade={"persist"})
     */
    private $ips;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProducts(): ?Product
    {
        return $this->products;
    }

    public function setProducts(?Product $products): self
    {
        $this->products = $products;

        return $this;
    }

    public function getIps(): ?UserIp
    {
        return $this->ips;
    }

    public function setIps(?UserIp $ips): self
    {
        $this->ips = $ips;

        return $this;
    }
}
