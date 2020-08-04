<?php


namespace App\Controller;

use App\Entity\AdminShopsRules;
use App\Repository\AdminShopsRulesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShopRulesController extends AbstractController
{
    /**
     * @Route("/admin/shop_rules", name="shop_rules")
     */
    public function listAction(AdminShopsRulesRepository $repository)
    {
        /** @var AdminShopsRules[] $adminShopsRules */
        $adminShopsRules = $repository->findAll();
        
        return $this->render('shop_rules/list.html.twig', [
            'rules' => $adminShopsRules    
        ]);
    }
}