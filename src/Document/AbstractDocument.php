<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

abstract class AbstractDocument
{
    /**
     * @MongoDB\Id
     */
    protected $id;
    
    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $decline = false;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $declineReasonClass;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $shop;

    /**
     * @return bool
     */
    public function isDecline(): bool
    {
        return $this->decline;
    }

    /**
     * @param bool $decline
     * @return AbstractDocument
     */
    public function setDecline(bool $decline): AbstractDocument
    {
        $this->decline = $decline;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeclineReasonClass()
    {
        return $this->declineReasonClass;
    }

    /**
     * @param mixed $declineReasonClass
     * @return AbstractDocument
     */
    public function setDeclineReasonClass($declineReasonClass)
    {
        $this->declineReasonClass = $declineReasonClass;
        return $this;
    }
}