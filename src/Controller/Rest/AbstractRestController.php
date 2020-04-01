<?php

namespace App\Controller\Rest;

use App\Services\Helpers;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractRestController extends AbstractFOSRestController
{
    /**
     * @var Helpers
     */
    private $helpers;

    /**
     * AbstractRestController constructor.
     * @param Helpers $helpers
     */
    public function __construct(Helpers $helpers)
    {
        $this->helpers = $helpers;
    }

    /**
     * @param $data
     * @param null|array $groups
     * @param null|bool $withEmptyField
     *
     * @return View
     */
    protected function createSuccessResponse($data, array $groups = null, $withEmptyField = null)
    {
        $context = new Context();
        if ($groups) {
            $context->setGroups($groups);
        }

        if ($withEmptyField) {
            $context->setSerializeNull(true);
        }

        return View::create()
            ->setStatusCode(Response::HTTP_OK)
            ->setData($data)
            ->setContext($context);
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param $key
     * @param $data
     *
     * @return ParamFetcher
     */
    protected function setParamFetcherData(
        ParamFetcher $paramFetcher,
        $key,
        $data,
        string $type = 'query'
    )
    {
        $type = $this->getHelpers()
            ->white_list($type, ["query", "request"], "Invalid type " . $type);

        $request = $this->get('request_stack')->getCurrentRequest();
        $request->query->set($key, $data);
        $param = new QueryParam();
        $param->name = $key;
        $paramFetcher->addParam($param);

        return $paramFetcher;
    }

    /**
     * @return Helpers
     */
    public function getHelpers(): Helpers
    {
        return $this->helpers;
    }
}
