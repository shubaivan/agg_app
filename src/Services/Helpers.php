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
        $result = preg_replace('!\s+!', ' ', $searchField);
        $result = preg_replace('/\s*,\s*/', ',', $result);
        $result = preg_replace('!\s!', '&', $result);

        $delimiter = ($strict !== true ? ':*|' : '|');

        if (preg_match_all('/[,]/', $searchField, $matches) > 0) {
            $search = str_replace(',', $delimiter, $result) . ($strict !== true ? ':*' : '');
        } else {
            $search = $result . ($strict !== true ? ':*' : '');
        }

        $search = str_replace(':*|:*|', ':*|', $search);

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

    /**
     * PHP encrypt and decrypt example
     *
     * Simple method to encrypt or decrypt a plain text string initialization
     * vector(IV) has to be the same when encrypting and decrypting in PHP 5.4.9.
     *
     * @link http://naveensnayak.wordpress.com/2013/03/12/simple-php-encrypt-and-decrypt/
     *
     * @param string $action Acceptable values are `encrypt` or `decrypt`.
     * @param string $string The string value to encrypt or decrypt.
     * @return string
     */
    public function encrypt_decrypt($action, $string)
    {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a
        // warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else {
            if ($action == 'decrypt') {
                $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
            }
        }

        return $output;
    }
}