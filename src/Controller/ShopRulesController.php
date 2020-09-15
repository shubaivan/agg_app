<?php


namespace App\Controller;

use App\Entity\AdminShopsRules;
use App\Entity\Brand;
use App\Repository\AdminShopsRulesRepository;
use App\Repository\BrandRepository;
use App\Repository\ShopRepository;
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
     * ShopRulesController constructor.
     * @param AdminShopsRulesRepository $adminShopsRulesRepository
     * @param SerializerInterface $serializer
     * @param ShopRepository $shopRepository
     */
    public function __construct(
        AdminShopsRulesRepository $adminShopsRulesRepository,
        SerializerInterface $serializer,
        ShopRepository $shopRepository
    )
    {
        $this->adminShopsRulesRepository = $adminShopsRulesRepository;
        $this->serializer = $serializer;
        $this->shopRepository = $shopRepository;
    }

    /**
     * @Route("/admin/shop_rules", name="shop_rules")
     */
    public function listAction()
    {
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

        $parameterBag = new ParameterBag();
        $shopNames = $this->shopRepository
            ->getAvailableShoForCreatingRule($parameterBag, ['shopName', 'id']);
        $prepareSelectShopName = [];
        array_walk($shopNames, function ($v) use (&$prepareSelectShopName) {
           if (isset($v['shopName']) && isset($v['id'])) {
               $prepareSelectShopName[$v['id']] = $v['shopName'];
           }
        });
        // Render the twig view
        return $this->render('admin/shop_rule_list.html.twig', [
            'th_keys' => $keys,
            'dataTbaleKeys' => $dataTableColumnData,
            'img_columns' => AdminShopsRules::getImageColumns(),
            'link_columns' => AdminShopsRules::getLinkColumns(),
            'short_preview_columns' => AdminShopsRules::getShortPreviewText(),
            'separate_filter_column' => AdminShopsRules::getSeparateFilterColumn(),
            'convert_to_html_columns' => AdminShopsRules::convertToHtmColumns(),
            'prepareSelectShopName' => $prepareSelectShopName
        ]);
    }
}