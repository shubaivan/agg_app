<?php

namespace App\Controller;

use App\Document\AbstractDocument;
use App\Document\AdrecordProduct;
use App\Document\AdtractionProduct;
use App\Document\AwinProduct;
use Doctrine\ODM\MongoDB\DocumentManager;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            'decline_reason' => AdtractionProduct::getDeclineReasonKey()
        ]);
    }
    
    /**
     * @Route("/admin/product_collection/awin", name="product_collection_awin")
     */
    public function awinProductCollectionList()
    {
        $oneAwinProduct = $this->documentManager
            ->getRepository(AwinProduct::class)->findOneBy([]);
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
            'decline_reason' => AdtractionProduct::getDeclineReasonKey()
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
            'decline_reason' => AdrecordProduct::getDeclineReasonKey()
        ]);
    }    
}