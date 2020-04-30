<?php


namespace App\View;


use App\Cache\CacheManager;
use App\Util\RedisHelper;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonHandler
{
    /**
     * @var RedisHelper
     */
    private $redisHelper;

    /**
     * JsonHandler constructor.
     * @param RedisHelper $redisHelper
     */
    public function __construct(RedisHelper $redisHelper)
    {
        $this->redisHelper = $redisHelper;
    }


    /**
     * @param ViewHandler $handler
     * @param View $view
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function createResponse(ViewHandler $handler, View $view, Request $request)
    {
        $expiresTime = (int) $this->redisHelper->get(CacheManager::HTTP_CACHE_EXPIRES_TIME);
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

        $response = $handler->createResponse($view, $request, 'json');

        $response
            ->setExpires($date);

        return $response;
    }
}