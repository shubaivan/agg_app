<?php


namespace App\Controller\Rest\Admin;

use App\Controller\Rest\AbstractRestController;
use App\Services\Helpers;
use App\Services\StatisticsService;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class StatisticsMonitoringController extends AbstractRestController
{
    /**
     * @var StatisticsService
     */
    private $statisticsService;

    /**
     * StatisticsMonitoringController constructor.
     * @param Helpers $helpers
     * @param StatisticsService $statisticsService
     */
    public function __construct(Helpers $helpers,
                                StatisticsService $statisticsService)
    {
        parent::__construct($helpers);
        $this->statisticsService = $statisticsService;
    }

    /**
     * get Statistics info.
     *
     * @Rest\Post("/admin/api/all_statistics", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Exception
     */
    public function statisticsListAction(Request $request)
    {
        $htmlAllStatistics = $this->statisticsService->getHtmlAllStatistics();
        $view = $this->createSuccessResponse(['data' => $htmlAllStatistics]);

        return $view;
    }

}