<?php


namespace App\Entity\Models;

use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

class SentEmail
{
    /**
     * @var string
     * @Annotation\Type("string")
     * @Assert\NotBlank()
     */
    private $fullName;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Assert\NotBlank()
     */
    private $storeName;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Assert\NotBlank()
     */
    private $message;

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getStoreName(): string
    {
        return $this->storeName;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}