<?php


namespace App\Controller;

use App\Repository\CategoryConfigurationsRepository;
use App\Repository\CategoryRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Document\AdtractionProduct;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HoverMenuController extends AbstractController
{
    /**
     * @Route("/admin/hover_menu/categories", name="hover_menu_categories")
     */
    public function getHoverMenuCategories()
    {
        $dataTableColumnData = [];
        $keys = [
            'CategoryName',
            'PositiveKeyWords',
            'NegativeKeyWords',
            'Action'
        ];
        array_map(function ($k) use (&$dataTableColumnData) {
            $dataTableColumnData[] = ['data' => $k];
        }, $keys);

        // Render the twig view
        return $this->render(
            'hover_menu/hover_menu_list.html.twig',
            [
                'th_keys' => $keys,
                'dataTbaleKeys' => $dataTableColumnData,
            ]
        );
    }
}