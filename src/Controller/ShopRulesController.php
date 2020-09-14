<?php


namespace App\Controller;

use App\Entity\AdminShopsRules;
use App\Entity\Brand;
use App\Repository\AdminShopsRulesRepository;
use App\Repository\BrandRepository;
use App\Repository\ShopRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
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
     * ShopRulesController constructor.
     * @param AdminShopsRulesRepository $adminShopsRulesRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(AdminShopsRulesRepository $adminShopsRulesRepository, SerializerInterface $serializer)
    {
        $this->adminShopsRulesRepository = $adminShopsRulesRepository;
        $this->serializer = $serializer;
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


        // Render the twig view
        return $this->render('admin/shop_rule_list.html.twig', [
            'th_keys' => $keys,
            'dataTbaleKeys' => $dataTableColumnData,
            'img_columns' => AdminShopsRules::getImageColumns(),
            'link_columns' => AdminShopsRules::getLinkColumns(),
            'short_preview_columns' => AdminShopsRules::getShortPreviewText(),
            'separate_filter_column' => AdminShopsRules::getSeparateFilterColumn(),

            'convert_to_html_columns' => AdminShopsRules::convertToHtmColumns()
        ]);
    }
}