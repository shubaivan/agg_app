<?php

namespace App\Controller\Rest;

use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractFOSRestController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * ProductController constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Creates an Article resource
     * @Rest\Get("/products")
     * @param Request $request
     * @return View
     */
    public function getProducts(Request $request): View
    {
        $products = $this->getProductRepository()->findAll();
        // In case our GET was a success we need to return a 200 HTTP OK response with the request object
        return View::create($products, Response::HTTP_OK);
    }

    /**
     * @return ProductRepository
     */
    public function getProductRepository(): ProductRepository
    {
        return $this->productRepository;
    }
}