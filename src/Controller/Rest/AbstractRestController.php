<?php

namespace App\Controller\Rest;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractRestController extends AbstractFOSRestController
{
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
}
