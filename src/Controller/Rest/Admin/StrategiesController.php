<?php

namespace App\Controller\Rest\Admin;

use App\Cache\TagAwareQueryResultCacheBrand;
use App\Cache\TagAwareQueryResultCacheProduct;
use App\Cache\TagAwareQuerySecondLevelCacheBrand;
use App\Cache\TagAwareQuerySecondLevelCacheCategory;
use App\Entity\Brand;
use App\Entity\Strategies;
use App\Repository\CategoryConfigurationsRepository;
use App\Repository\CategoryRepository;
use App\Repository\FilesRepository;
use App\Repository\ProductRepository;
use App\Repository\StrategiesRepository;
use App\Services\Models\Shops\Strategies\Common\AbstractStrategy;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Controller\Rest\AbstractRestController;
use App\Repository\BrandRepository;
use App\Services\Helpers;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\DoctrineProvider;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class StrategiesController extends AbstractRestController
{
    /**
     * @var StrategiesRepository
     */
    private $strategiesRepository;

    /**
     * StrategiesController constructor.
     * @param Helpers $helpers
     * @param StrategiesRepository $strategiesRepository
     */
    public function __construct(
        Helpers $helpers,
        StrategiesRepository $strategiesRepository
    )
    {
        parent::__construct($helpers);

        $this->strategiesRepository = $strategiesRepository;
    }

    /**
     * get Strategies.
     *
     * @Rest\Post("/admin/api/strategies_list", options={"expose": true})
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
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getStrategiesListAction(Request $request)
    {
        $parameterBag = new ParameterBag($request->request->all());
        $data = $this->strategiesRepository
            ->getCategoriesForSelect2($parameterBag);

        $more = $parameterBag->get('page') * 25 < $this->strategiesRepository
                ->getCategoriesForSelect2($parameterBag, true);
        $view = $this->createSuccessResponse(
            array_merge(
                [
                    "pagination" => [
                        'more' => $more
                    ],
                ],
                ['results' => $data]
            )
        );

        return $view;
    }

    /**
     * get Strategy bu slug.
     *
     * @Rest\Get("/admin/api/strategy/{slug}", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK, serializerGroups={Strategies::SERIALIZED_GROUP_GET_BY_SLUG})
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return Strategies
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getStrategyBySlugAction(Strategies $strategies)
    {
        return $strategies;
    }

    /**
     * apply Strategy bu slug.
     *
     * @Rest\Post("/admin/api/strategy/apply", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK, serializerGroups={Strategies::SERIALIZED_GROUP_GET_BY_SLUG})
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return array
     * @throws \ReflectionException
     */
    public function postApplyStrategyBySlugAction(Request $request)
    {
        $slug = $request->get('strategy_slug');
        $strategy = $this->strategiesRepository
            ->findOneBy(['slug' => $slug]);
        $coreAnalysis = false;
        if ($strategy) {
            $strategyNameSpace = $strategy->getStrategyNameSpace();
            $requiredArgs = $strategy->getRequiredArgs();
            $requiredArgsData = [];
            foreach ($requiredArgs as $arg) {
                $requiredArgsData[$arg] = $request->get($arg);
            }
            $reflector = new \ReflectionClass($strategyNameSpace);
            /** @var AbstractStrategy $instance */
            $instance = $reflector->newInstanceArgs($requiredArgsData);

            $requiredInputs = $strategy->getRequiredInputs();
            $requiredInputsData = [];
            foreach ($requiredInputs as $input) {
                $requiredInputsData[$input] = $request->get($input);
            }
            $coreAnalysis = $instance->coreAnalysis($requiredInputsData);
        }

        return ['result' => $coreAnalysis];
    }
}