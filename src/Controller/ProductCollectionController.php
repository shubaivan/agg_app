<?php

namespace App\Controller;

use App\Document\AbstractDocument;
use App\Document\AdrecordProduct;
use App\Document\AdtractionProduct;
use App\Document\AwinProduct;
use App\Document\TradeDoublerProduct;
use Doctrine\ODM\MongoDB\DocumentManager;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductCollectionController extends AbstractController
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var SerializerInterface $serializer
     */
    private $serializer;
    
    /**
     * ProductCollectionController constructor.
     */
    public function __construct(DocumentManager $documentManager, SerializerInterface $serializer)
    {
        $this->documentManager = $documentManager;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/admin/product_collection/adtraction", name="product_collection_adtraction")
     */
    public function adtractionProductCollectionList()
    {
        $oneProduct = $this->documentManager
            ->getRepository(AdtractionProduct::class)->findOneBy([]);
        $serialize = '{}';
        if ($oneProduct) {
            $serialize = $this->serializer->serialize(
                $oneProduct,
                'json'
            );
        }
        $json_decode = json_decode($serialize, true);
        $dataTableColumnData = [];
        $keys = array_keys($json_decode);
        array_map(function ($k) use (&$dataTableColumnData) {
            $dataTableColumnData[] = ['data' => $k];
        }, $keys);


        // Render the twig view
        return $this->render('products/collections/adtraction_list_custom.html.twig', [
            'th_keys' => $keys,
            'dataTbaleKeys' => $dataTableColumnData,
            'img_columns' => AdtractionProduct::getImageColumns(),
            'link_columns' => AdtractionProduct::getLinkColumns(),
            'short_preview_columns' => AdtractionProduct::getShortPreviewText(),
            'separate_filter_column' => AdtractionProduct::getSeparateFilterColumn(),
            'decline_reason' => AdtractionProduct::getDeclineReasonKey(),
            'convert_to_html_columns' => AdtractionProduct::convertToHtmColumns()
        ]);
    }


    /**
     * @Route(
     *     "/admin/product_collection/awin/dictDeclineError",
     *      name="product_collection_awin_dictDeclineError",
     *     options={"expose": true},
     *     methods={"POST"}
     * )
     */
    public function awinDictDeclineError(Request $request)
    {
        $collectionName = $request->get('collectionName');
        $objectRepository = $this->documentManager
            ->getRepository(AwinProduct::class);
        $dictDeclineError = $objectRepository->dictDeclineError($collectionName);
        
        return new JsonResponse($dictDeclineError);
    }
    
    /**
     * @Route("/admin/product_collection/awin", name="product_collection_awin")
     */
    public function awinProductCollectionList()
    {
        $objectRepository = $this->documentManager
            ->getRepository(AwinProduct::class);
        $oneAwinProduct = $objectRepository->findOneBy([]);
        $serialize = '{}';
        if ($oneAwinProduct) {
            $serialize = $this->serializer->serialize(
                $oneAwinProduct,
                'json'
            );   
        }
        $json_decode = json_decode($serialize, true);
        $dataTableColumnData = [];
        $keys = array_keys($json_decode);
        array_map(function ($k) use (&$dataTableColumnData) {
            $dataTableColumnData[] = ['data' => $k];
        }, $keys);
        
        // Render the twig view
        return $this->render('products/collections/awin_list_custom.html.twig', [
            'th_keys' => $keys,
            'dataTbaleKeys' => $dataTableColumnData,
            'img_columns' => AwinProduct::getImageColumns(),
            'link_columns' => AwinProduct::getLinkColumns(),
            'short_preview_columns' => AwinProduct::getShortPreviewText(),
            'separate_filter_column' => AwinProduct::getSeparateFilterColumn(),
            'decline_reason' => AwinProduct::getDeclineReasonKey(),
            'convert_to_html_columns' => AwinProduct::convertToHtmColumns()
        ]);
    }

    /**
     * @Route("/admin/product_collection/adrecord", name="product_collection_adrecord")
     */
    public function adrecordProductCollectionList()
    {
        $oneProduct = $this->documentManager
            ->getRepository(AdrecordProduct::class)->findOneBy([]);
        $serialize = '{}';
        if ($oneProduct) {
            $serialize = $this->serializer->serialize(
                $oneProduct,
                'json'
            );
        }
        $json_decode = json_decode($serialize, true);
        $dataTableColumnData = [];
        $keys = array_keys($json_decode);
        array_map(function ($k) use (&$dataTableColumnData) {
            $dataTableColumnData[] = ['data' => $k];
        }, $keys);


        // Render the twig view
        return $this->render('products/collections/adrecord_list_custom.html.twig', [
            'th_keys' => $keys,
            'dataTbaleKeys' => $dataTableColumnData,
            'img_columns' => AdrecordProduct::getImageColumns(),
            'link_columns' => AdrecordProduct::getLinkColumns(),
            'short_preview_columns' => AdrecordProduct::getShortPreviewText(),
            'separate_filter_column' => AdrecordProduct::getSeparateFilterColumn(),
            'decline_reason' => AdrecordProduct::getDeclineReasonKey(),
            'convert_to_html_columns' => AdrecordProduct::convertToHtmColumns()
        ]);
    }

    /**
     * @Route("/admin/product_collection/trade_doubler", name="product_collection_trade_doubler")
     */
    public function tradeDoublerProductCollectionList()
    {
        $oneProduct = $this->documentManager
            ->getRepository(TradeDoublerProduct::class)->findOneBy([]);
        $serialize = '{}';
        if ($oneProduct) {
            $serialize = $this->serializer->serialize(
                $oneProduct,
                'json',
                SerializationContext::create()->setGroups(TradeDoublerProduct::GROUP_GET_TH)
            );
        }
        $json_decode = json_decode($serialize, true);
        $dataTableColumnData = [];
        $keys = array_keys($json_decode);
        array_map(function ($k) use (&$dataTableColumnData) {
            $dataTableColumnData[] = ['data' => $k];
        }, $keys);


        // Render the twig view
        return $this->render('products/collections/trade_doubler_list_custom.html.twig', [
            'th_keys' => $keys,
            'dataTbaleKeys' => $dataTableColumnData,
            'img_columns' => TradeDoublerProduct::getImageColumns(),
            'array_columns' => TradeDoublerProduct::arrayColumns(),
            'link_columns' => TradeDoublerProduct::getLinkColumns(),
            'short_preview_columns' => TradeDoublerProduct::getShortPreviewText(),
            'separate_filter_column' => TradeDoublerProduct::getSeparateFilterColumn(),
            'decline_reason' => TradeDoublerProduct::getDeclineReasonKey(),
            'convert_to_html_columns' => TradeDoublerProduct::convertToHtmColumns()
        ]);
    }
}