<?php


namespace App\Controller;

use App\Entity\Brand;
use App\Repository\BrandRepository;
use App\Repository\CategoryConfigurationsRepository;
use App\Repository\CategoryRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Document\AdtractionProduct;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BrandsController extends AbstractController
{
    /**
     * @var BrandRepository
     */
    private $brandRepository;

    /**
     * @var SerializerInterface $serializer
     */
    private $serializer;

    /**
     * BrandsController constructor.
     * @param BrandRepository $brandRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(BrandRepository $brandRepository, SerializerInterface $serializer)
    {
        $this->brandRepository = $brandRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/admin/brands/management", name="brands_management")
     */
    public function getListBrands()
    {
        $oneProduct = $this->brandRepository
            ->findOneBy([]);
        $serialize = '{}';
        if ($oneProduct) {
            $serialize = $this->serializer->serialize(
                $oneProduct,
                'json',
                SerializationContext::create()->setGroups(Brand::SERIALIZED_GROUP_LIST_TH)
            );
        }
        
        $json_decode = json_decode($serialize, true);
        $dataTableColumnData = [];
        $keys = array_keys($json_decode);
        array_map(function ($k) use (&$dataTableColumnData) {
            $dataTableColumnData[] = ['data' => $k];
        }, $keys);
        
        
        // Render the twig view
        return $this->render('admin/brand_list.html.twig', [
            'th_keys' => $keys,
            'dataTbaleKeys' => $dataTableColumnData,
            'img_columns' => Brand::getImageColumns(),
            'link_columns' => Brand::getLinkColumns(),
            'short_preview_columns' => Brand::getShortPreviewText(),
            'separate_filter_column' => Brand::getSeparateFilterColumn(),
            
            'convert_to_html_columns' => Brand::convertToHtmColumns(),

            'seo_columns' => Brand::getSeoRenderColumns(),
        ]);
    }
}