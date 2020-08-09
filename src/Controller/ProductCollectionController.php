<?php

namespace App\Controller;

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
     * @Route("/admin/product_collection/awin", name="product_collection_awin")
     */
    public function awinProductCollectionList(Request $request, PaginatorInterface $paginator)
    {
        $oneAwinProduct = $this->documentManager
            ->getRepository(AwinProduct::class)->findOneBy([]);

        $serialize = $this->serializer->serialize($oneAwinProduct, 'json');
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
            'img_columns' => AwinProduct::getImageColumns()
        ]);
    }
}