<?php

namespace App\Services\Models;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CategoryService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * CategoryService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Product $product
     * @return Category[]
     * @throws \Doctrine\ORM\ORMException
     */
    public function createCategoriesFromProduct(Product $product)
    {
        $stringCategories = $product->getCategory();
        if (strlen($stringCategories) < 1) {
            throw new BadRequestHttpException('product id:' . $product->getId() . ' category is empty');
        }
        $matchAll = preg_match_all('/[-]/', $stringCategories, $matches);
        $arrayCategories = [];
        if ($matchAll > 0) {
            $arrayCategories = explode(' - ', $stringCategories);
        } else {
            array_push($arrayCategories, $stringCategories);
        }

        return $this->createArrayModelCategory($arrayCategories, $product);
    }

    /**
     * @param array $arrayCategories
     * @param Product $product
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    private function createArrayModelCategory(array $arrayCategories, Product $product)
    {
        $arrayModelsCategory = [];
        foreach ($arrayCategories as $category) {
            $categoryModel = $this->matchExistCategory($category);
            if (!($categoryModel instanceof Category)) {
                $categoryModel = new Category();
                $categoryModel
                    ->setName($category);
            }
            $product->addCategoryRelation($categoryModel);
            array_push($arrayModelsCategory, $categoryModel);
        }

        return $arrayModelsCategory;
    }

    /**
     * @param string $name
     * @return Category|object|null
     */
    private function matchExistCategory(string $name)
    {
        return $this->getEm()->getRepository(Category::class)
            ->findOneBy(['name' => $name]);
    }

    /**
     * @return EntityManager
     */
    private function getEm(): EntityManager
    {
        return $this->em;
    }
}