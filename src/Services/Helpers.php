<?php


namespace App\Services;

use App\Cache\CacheManager;
use App\Util\RedisHelper;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Helpers
{
    /**
     * @var Serializer
     */
    private $serilizer;

    /**
     * @var RedisHelper
     */
    private $redisHelper;

    /**
     * Helpers constructor.
     * @param SerializerInterface $serilizer
     * @param RedisHelper $redisHelper
     */
    public function __construct(
        SerializerInterface $serilizer,
        RedisHelper $redisHelper
    )
    {
        $this->serilizer = $serilizer;
        $this->redisHelper = $redisHelper;
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

    /**
     * @return \DateTime|false
     * @throws \Exception
     */
    public function getExpiresHttpCache()
    {
        $expiresTime = (int) $this->redisHelper
            ->get(CacheManager::HTTP_CACHE_EXPIRES_TIME);
        $expiresTimeDateTime = new \DateTime();
        if ($expiresTime) {
            $expiresTimeDateTime->setTimestamp($expiresTime);
        }

        $middleDay = (new \DateTime('today'))->setTime(12, 00, 00);
        if ($expiresTimeDateTime < $middleDay) {
            $date = (new \DateTime('today'))->setTime(11, 59, 00);
        } else {
            $date = (new \DateTime('today'))->setTime(23, 59, 00);
        }

        return $date;
    }
}