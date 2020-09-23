<?php


namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticsMonitoring extends AbstractController
{
    /**
     * @Route("/admin/statistics/monitoring", name="admin_statistics_monitoring")
     */
    public function resourceShopList()
    {
        // Render the twig view
        return $this->render('admin/statistics_monitoring.html.twig', [
        ]);
    }
}