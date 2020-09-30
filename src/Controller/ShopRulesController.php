<?php


namespace App\Controller;

use App\Entity\AdminShopsRules;
use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Shop;
use App\Repository\AdminShopsRulesRepository;
use App\Repository\BrandRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use Doctrine\DBAL\Cache\CacheException;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShopRulesController extends AbstractController
{
    /**
     * @var AdminShopsRulesRepository
     */
    private $adminShopsRulesRepository;

    /**
     * @var SerializerInterface $serializer
     */
    private $serializer;

    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * ShopRulesController constructor.
     * @param AdminShopsRulesRepository $adminShopsRulesRepository
     * @param SerializerInterface $serializer
     * @param ShopRepository $shopRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        AdminShopsRulesRepository $adminShopsRulesRepository,
        SerializerInterface $serializer,
        ShopRepository $shopRepository,
        ProductRepository $productRepository
    )
    {
        $this->adminShopsRulesRepository = $adminShopsRulesRepository;
        $this->serializer = $serializer;
        $this->shopRepository = $shopRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/admin/shop_rules", name="shop_rules")
     * @throws CacheException
     */
    public function listAction()
    {
        $product = $this->productRepository->findOneBy([]);
        if ($product) {
            $serialize = $this->serializer->serialize(
                $product,
                'json',
                SerializationContext::create()
                    ->setGroups(Product::SERIALIZED_GROUP_SHOP_RULES)
            );

            $productsColumnRules['common'] = array_keys(json_decode($serialize, true));
        }

        $productsColumnRules['extras'] = $this->productRepository
            ->getUniqExtraKeys();
        $entity = $this->adminShopsRulesRepository
            ->findOneBy([]);
        $serialize = '{}';
        if ($entity) {
            $serialize = $this->serializer->serialize(
                $entity,
                'json',
                SerializationContext::create()->setGroups(AdminShopsRules::GROUP_LIST_TH)
            );
        }

        $json_decode = json_decode($serialize, true);
        $dataTableColumnData = [];
        $keys = array_keys($json_decode);
        array_map(function ($k) use (&$dataTableColumnData) {
            $dataTableColumnData[] = ['data' => $k];
        }, $keys);

        $availableStoreNames = $this->adminShopsRulesRepository
            ->getAvailableStoreNames();
        $prepareExcludeShopNames = [];
        array_walk($availableStoreNames, function ($v) use (&$prepareExcludeShopNames) {
            if (isset($v['store'])) {
                $prepareExcludeShopNames[] = $v['store'];
            }
        });
        $prepareSelectShopName = [];
        foreach (Shop::getGroupShopNamesMapping() as $key=>$resourceGroup)
        {
            $filters = array_filter($resourceGroup, function ($v) use (&$prepareExcludeShopNames) {
                if (array_search($v, $prepareExcludeShopNames)) {
                    return false;
                } else {
                    return true;
                }
            });
            if (count($filters)) {
                $prepareSelectShopName[$key] = $filters;
            }
        }

        // Render the twig view
        return $this->render('admin/shop_rule_list.html.twig', [
            'th_keys' => $keys,
            'dataTbaleKeys' => $dataTableColumnData,
            'img_columns' => AdminShopsRules::getImageColumns(),
            'link_columns' => AdminShopsRules::getLinkColumns(),
            'short_preview_columns' => AdminShopsRules::getShortPreviewText(),
            'separate_filter_column' => AdminShopsRules::getSeparateFilterColumn(),
            'convert_to_html_columns' => AdminShopsRules::convertToHtmColumns(),
            'prepareSelectShopName' => $prepareSelectShopName,
            'productsColumnRules' => $productsColumnRules
        ]);
    }
}