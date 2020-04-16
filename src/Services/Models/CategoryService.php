<?php

namespace App\Services\Models;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Services\ObjectsHandler;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryService
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var ObjectsHandler
     */
    private $objecHandler;

    /**
     * CategoryService constructor.
     * @param CategoryRepository $categoryRepository
     * @param ObjectsHandler $objecHandler
     */
    public function __construct(CategoryRepository $categoryRepository, ObjectsHandler $objecHandler)
    {
        $this->categoryRepository = $categoryRepository;
        $this->objecHandler = $objecHandler;
    }

    /**
     * @param Product $product
     * @return array
     * @throws \App\Exception\ValidatorException
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
            $arrayCategories = array_unique(explode(' - ', $stringCategories));
        } else {
            array_push($arrayCategories, $stringCategories);
        }

        return $this->createArrayModelCategory($arrayCategories, $product);
    }

    /**
     * @param array $arrayCategories
     * @param Product $product
     * @return array
     * @throws \App\Exception\ValidatorException
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

                $this->getObjecHandler()
                    ->validateEntity($categoryModel, [Category::SERIALIZED_GROUP_CREATE]);
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
        return $this->getCategoryRepository()
            ->findOneBy(['name' => $name]);
    }

    /**
     * @return ObjectsHandler
     */
    private function getObjecHandler(): ObjectsHandler
    {
        return $this->objecHandler;
    }

    /**
     * @return CategoryRepository
     */
    private function getCategoryRepository(): CategoryRepository
    {
        return $this->categoryRepository;
    }
}