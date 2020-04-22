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

    /**
     * @param $searchField
     * @param bool $strict
     * @return string
     */
    public function handleSearchValue($searchField, bool $strict): string
    {
        if (preg_match_all('/[,]/', $searchField, $matches) > 0) {
            $result = preg_replace('!\s+!', ' ', $searchField);
            $result = preg_replace('/\s*,\s*/', ',', $result);
            $result = preg_replace('!\s!', '&', $result);
            $search = str_replace(',', ':*|', $result) . ':*';
        } else {
            $result = preg_replace('!\s+!', ' ', $searchField);
            $result = preg_replace('!\s!', '&', $result);
            $search = $result . ($strict !== true ? ':*' : '');
        }
        return $search;
    }
}