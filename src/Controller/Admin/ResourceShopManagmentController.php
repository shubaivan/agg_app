<?php

namespace App\Controller\Admin;

use App\Entity\Shop;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ResourceShopManagmentController extends AbstractController
{
    const DATA_TABLES_TH = [
        self::RESOURCE_NAME,
        'ShopName',
        self::PRODUCTS_QUANTITY,
        self::ACTION
    ];
    const PRODUCTS_QUANTITY = 'GroupedProductsQuantity';
    const ACTION = 'Action';
    const RESOURCE_NAME = 'ResourceName';

    /**
     * @Route("/admin/resource/shop/list", name="admin_resource_shop_list")
     */
    public function resourceShopList()
    {
        $shopNamesMapping = Shop::getGroupShopNamesMapping();
        $dataTableColumnData = [];
        array_map(function ($k) use (&$dataTableColumnData) {
            $dataTableColumnData[] = ['data' => $k];
        }, self::DATA_TABLES_TH);

        // Render the twig view
        return $this->render('admin/resource_shop_list.html.twig', [
            'th_keys' => self::DATA_TABLES_TH,
            'dataTbaleKeys' => $dataTableColumnData,
        ]);
    }
}