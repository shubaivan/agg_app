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
    private static $th_hover_menu = [
        'CategoryName',
        'PositiveKeyWords',
        'NegativeKeyWords',
        'Action'
    ];
    
    /**
     * @Route("/admin/hover_menu/categories", name="hover_menu_categories")
     */
    public function getHoverMenuCategories()
    {
        $dataTableColumnData = [];
        $keys = self::$th_hover_menu;
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

    /**
     * @return array
     */
    public static function getThHoverMenu(): array
    {
        return self::$th_hover_menu;
    }
    
    
}