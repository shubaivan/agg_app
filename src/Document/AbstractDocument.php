<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation;

abstract class AbstractDocument implements DataTableInterface
{
    /**
     * @MongoDB\Id
     */
    protected $id;
    
    /**
     * @MongoDB\Field(type="boolean")
     * @Annotation\Accessor(getter="getDeclineAccessor")
     */
    protected $decline = false;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $declineReasonClass = '';

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

    public function getDeclineAccessor()
    {
        return $this->decline ? 'true' : 'false';
    }

    public static function getDeclineReasonKey()
    {
        return ['declineReasonClass'];
    }

    public static function getSeparateFilterColumn():array
    {
        return [
            'shop', 'decline'
        ];
    }
}