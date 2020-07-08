<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait LoginTimestampable
 * @package App\Entity
 */
trait LoginTimestampable
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $loginAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $logoutAt;

    /**
     * @return \DateTime
     */
    public function getLoginAt(): \DateTime
    {
        return $this->loginAt;
    }

    /**
     * @param \DateTime $loginAt
     * @return object
     */
    public function setLoginAt(\DateTime $loginAt)
    {
        $this->loginAt = $loginAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLogoutAt(): \DateTime
    {
        return $this->logoutAt;
    }

    /**
     * @param \DateTime $logoutAt
     * @return object
     */
    public function setLogoutAt(\DateTime $logoutAt)
    {
        $this->logoutAt = $logoutAt;
        return $this;
    }
}
