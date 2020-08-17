<?php

namespace App\Controller\Rest;

use App\Document\AdrecordProduct;
use App\Document\AwinProduct;
use App\Document\TradeDoublerProduct;
use App\Exception\ValidatorException;
use App\Services\Helpers;
use Doctrine\DBAL\DBALException;
use Doctrine\ODM\MongoDB\DocumentManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class TradeDoublerCollectionController extends AbstractRestController
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * ProductCollectionController constructor.
     */
    public function __construct(DocumentManager $documentManager, Helpers $helpers)
    {
        parent::__construct($helpers);

        $this->documentManager = $documentManager;
    }

    /**
     * get Products.
     *
     * @Rest\Post("/api/colleciton/trade_doubler", options={"expose": true})
     *
     * @param ParamFetcher $paramFetcher
     *
     * @View(statusCode=Response::HTTP_OK)
     *
     * @SWG\Tag(name="Products")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object Products",
     * )
     *
     * @return \FOS\RestBundle\View\View
     * @throws DBALException
     * @throws ValidatorException
     */
    public function postProductsAction(Request $request)
    {
        $dataTable = $this->documentManager
            ->getRepository(TradeDoublerProduct::class)
            ->getDataTableAggr($request->request->all());


        foreach ($dataTable['data'] as $key=>$data) {
            if (isset($data['_id'])) {
                $array = (array)$data['_id'];
                unset($dataTable['data'][$key]['_id']);
                $dataTable['data'][$key]['id'] = array_shift($array);
            }
        }

        $view = $this->createSuccessResponse(
            array_merge(
                [
                    "draw" => $request->request->get('draw'),
                    "recordsTotal" => $this->documentManager
                        ->getRepository(TradeDoublerProduct::class)->getCountDoc(),
                    "recordsFiltered"=> $dataTable['count'] ? $dataTable['count'] : $this->documentManager
                        ->getRepository(TradeDoublerProduct::class)->getCountDoc()
                ],
                ['data' => $dataTable['data']]
            )
        );

        return $view;
    }
}