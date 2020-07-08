<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="home_index")
     */
    public function home()
    {
        $r = 1;
        if ($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_ADMIN')) {
            return $this->render('index/index.html.twig', []);
        }

        return $this->render('home/index.html.twig', []);
    }

    /**
     * @Route("/index", name="index")
     */
    public function index()
    {
        $user = $this->getUser();
        $t = 1;
        return $this->render('index/index.html.twig', [
            'controller_name' => 'Catalog Serial',
        ]);
    }
}
