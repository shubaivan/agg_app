<?php


namespace App\Services;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Helpers
{
    /**
     * @var Serializer
     */
    private $serilizer;

    /**
     * Helpers constructor.
     * @param Serializer $serilizer
     */
    public function __construct(SerializerInterface $serilizer)
    {
        $this->serilizer = $serilizer;
    }

    /**
     * @param array $array
     * @return string
     */
    public function executeSerializerArray(array $array)
    {
        return $this->serilizer->serialize($array, 'json');
    }

    /**
     * @param $value
     * @param $allowed
     * @param $message
     * @return mixed
     * @throws BadRequestHttpException
     */
    function white_list(&$value, $allowed, $message)
    {
        if ($value === null) {
            return $allowed[0];
        }
        $key = array_search($value, $allowed, true);
        if ($key === false) {
            throw new BadRequestHttpException($message);
        } else {
            return $value;
        }
    }
}