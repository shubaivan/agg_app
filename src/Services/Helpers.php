<?php


namespace App\Services;

use App\Cache\CacheManager;
use App\Kernel;
use App\Util\RedisHelper;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelInterface;

class Helpers
{
    const THESAURUS_MY_SWEDISH_REGULARS = 'thesaurus_my_swedish_regulars';
    /**
     * @var Serializer
     */
    private $serilizer;

    /**
     * @var RedisHelper
     */
    private $redisHelper;

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * Helpers constructor.
     * @param SerializerInterface $serilizer
     * @param RedisHelper $redisHelper
     * @param KernelInterface $kernel
     */
    public function __construct(
        SerializerInterface $serilizer,
        RedisHelper $redisHelper,
        KernelInterface $kernel
    )
    {
        $this->serilizer = $serilizer;
        $this->redisHelper = $redisHelper;
        $this->kernel = $kernel;
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
    public function handleSearchValue(
        $searchField,
        bool $strict,
        bool $combineWordWithSpace = false,
        ?string $delimiter = null
    ): string
    {
        $result = str_replace('.', ',', $searchField);
        $result = str_replace('&', ' ', $result);
        $result = strip_tags($result);

        if (!$combineWordWithSpace) {
            $result = preg_replace('!\s+!', ' ', $result);
        }

        $result = preg_replace('/\s*,\s*/', ',', $result);

        if (!$combineWordWithSpace) {
            $result = preg_replace('!\s!', '&', $result);
        }

        $result = str_replace(',,', ',', $result);
        if (!$delimiter) {
            $delimiter = ($strict !== true ? ':*|' : '|');
        }

        $search = str_replace(',', $delimiter, $result) . ($strict !== true ? ':*' : '');

        $search = str_replace(':*|:*|', ':*|', $search);
//        $search = str_replace('", "', '|', $search);
//        if (preg_match_all("/\(.*?\):\*/", $search, $m)) {
//            $matchResults = array_shift($m);
//            foreach ($matchResults as $matchResult) {
//                $matchResultTransform = preg_replace("/\|/", '&', $matchResult);
//                $matchResultTransform = trim($matchResultTransform, ':*');
//                $search = str_replace($matchResult, $matchResultTransform, $search);
//            }
//        }

        return $search;
    }

    /**
     * @return \DateTime|false
     * @throws \Exception
     */
    public function getExpiresHttpCache()
    {
        $expiresTime = (int)$this->redisHelper
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
        $iv = mb_substr(hash('sha256', $secret_iv), 0, 16);

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

    /**
     * @param string $inputString
     * @return array
     */
    public function pregWordsFromDictionary(string $inputString)
    {
        $implode = $this->redisHelper->get(self::THESAURUS_MY_SWEDISH_REGULARS);
        if (!$implode) {
            $fileGetContents = file_get_contents(
                $this->kernel->getProjectDir() . '/pg/thesaurus_my_swedish.ths'
            );
            $explode = explode(PHP_EOL, $fileGetContents);
            $arrayMap = array_map(function ($v) {
                $explodeValue = explode(':', $v);
                if (count($explodeValue)) {
                    $currentWord = trim($explodeValue[0]);
                    if (strlen($currentWord) > 0) {
                        return $currentWord;
                    }
                }
            }, $explode);
            $arrayMap = array_filter($arrayMap, function ($value) {
                return !is_null($value) && $value !== '';
            }
            );
            $arrayMap = array_map(function ($v) {
                return '\b' . trim($v) . '\b';
            }, $arrayMap);
            $arrayMap = array_unique($arrayMap);
            $implode = implode('|', $arrayMap);

            $this->redisHelper
                ->set(self::THESAURUS_MY_SWEDISH_REGULARS, $implode, 31536000);
        }

        preg_match_all("/$implode/iu", $inputString, $mt);
        $result = preg_replace("/$implode/iu", '', $inputString);

        return [
            'match' => $mt,
            'result' => $result
        ];
    }

    /**
     * @param string $word
     */
    public function fillHoverMenuData(
        string $categoryName,
        string $type,
        string $word)
    {
        $categoryName = $this->transCategoryName($categoryName);

        if (!is_dir($this->kernel->getProjectDir() . '/hover_menu_categories_words')) {
            mkdir($this->kernel->getProjectDir() . '/hover_menu_categories_words');
        }

        if (!is_dir($this->kernel->getProjectDir() . '/hover_menu_categories_words/' . $categoryName)) {
            mkdir($this->kernel->getProjectDir() . '/hover_menu_categories_words/' . $categoryName);
        }

        $this->setDataInFile(
            $this->kernel->getProjectDir() . '/hover_menu_categories_words/' . $categoryName . '/' . $type,
            $word . ', '
        );
    }

    /**
     * @param string $categoryName
     * @param string $type
     * @return false|string
     */
    public function checkExistCategoryFile(string $categoryName, string $type)
    {
        $categoryName = $this->transCategoryName($categoryName);
        $filename = $this->kernel->getProjectDir() . '/hover_menu_categories_words/' . $categoryName . '/' . $type;
        if (file_exists($filename)) {
            $contents = file_get_contents($filename);

            return $contents;
        }

        return '';
    }

    /**
     * @param string $keyWords
     * @param string|null $categoryName
     * @param string|null $typeWord
     */
    public function handleKeyWords(
        string $keyWords, 
        ?string $categoryName = '', 
        ?string $typeWord = ''
    )
    {
        //        $keyWords = preg_replace('/\s+/', '', $keyWords);
        $keyWords = trim($keyWords);
        $keyWords = trim($keyWords, ',');
        $keyWords = preg_replace('/\n/', '', $keyWords);
//        $keyWords = preg_replace('!\s+!', '', $keyWords);

        $words = explode(',', $keyWords);
        if ($categoryName && $typeWord) {
            $this->reCreateHoverMenuData($categoryName, $typeWord);
        }
        foreach ($words as $key => $word) {
            $word = trim($word);
            
            if (!strlen($word)) {
                unset($words[$key]);
                continue;
            }

            if ($categoryName && $typeWord) {
                $this->fillHoverMenuData($categoryName, $typeWord, $word);
            }
        }
    }

    /**
     * @param string $categoryName
     * @param string $type
     */
    public function reCreateHoverMenuData(
        string $categoryName,
        string $type)
    {
        $categoryName = $this->transCategoryName($categoryName);
        if (file_exists($this->kernel->getProjectDir() . '/hover_menu_categories_words/' . $categoryName .'/'.$type)) {
            unlink($this->kernel->getProjectDir(). '/hover_menu_categories_words/' . $categoryName .'/'.$type);
        }
    }

    /**
     * @param string $path
     * @param string $data
     */
    private function setDataInFile(string $path, string $data): void
    {
        if (file_exists($path) && $data !== PHP_EOL) {
            if( exec('grep '.escapeshellarg(preg_replace('/\R/', '', $data)).' ' . $path)) {
                return;
            }
        }

        file_put_contents(
            $path,
            $data,
            FILE_APPEND
        );
    }

    /**
     * @param string $categoryName
     * @return mixed|string|string[]|null
     */
    private function transCategoryName(string $categoryName)
    {
        $categoryName = str_replace('&', '_', mb_strtolower($categoryName));
        $categoryName = preg_replace('!\s+!', '_', $categoryName);

        return $categoryName;
    }
}