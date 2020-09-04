<?php

namespace App\Controller;

use App\Document\AdrecordProduct;
use App\Document\AdtractionProduct;
use App\Document\AwinProduct;
use App\Document\TradeDoublerProduct;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="home_index")
     */
    public function home()
    {
        $r = 1;
        if ($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('index', []);
        }

        return $this->render('home/index.html.twig', []);
    }

    /**
     * @Route("/index", name="index")
     */
    public function index(DocumentManager $dm)
    {
        $user = $this->getUser();
        /** @var Router $obj */
        $obj = $this->get('router');
        $resourceDocuments = [
            [
                'name' => 'Awin',
                'count' => $dm->getRepository(AwinProduct::class)->getCountDoc(),
                'path' => $obj->generate('product_collection_awin')
            ],
            [
                'name' => 'Adrecord',
                'count' => $dm->getRepository(AdrecordProduct::class)->getCountDoc(),
                'path' => $obj->generate('product_collection_adrecord')
            ],
            [
                'name' => 'Adtraction',
                'count' => $dm->getRepository(AdtractionProduct::class)->getCountDoc(),
                'path' => $obj->generate('product_collection_adtraction')
            ],
            [
                'name' => 'TradeDoubler',
                'count' => $dm->getRepository(TradeDoublerProduct::class)->getCountDoc(),
                'path' => $obj->generate('product_collection_trade_doubler')
            ],
        ];
        
        return $this->render('index/index.html.twig', [
            'controller_name' => 'Catalog Serial',
            'resourceDocuments' => $resourceDocuments
        ]);
    }
}
