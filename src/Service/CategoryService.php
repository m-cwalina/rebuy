<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use App\Entity\Category;

class CategoryService
{
    private $categoryRepository;
    private $entityManager;
    private $serializer;
    private $validator;

    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function getGroupedCategories(): string
    {
      $categoriesRaw = $this->categoryRepository->findAll();

      $groupedCategories = [];
      foreach ($categoriesRaw as $category) {
          $key = $category->getCategory();

          if (!isset($groupedCategories[$key])) {
              $groupedCategories[$key] = [
                  'category' => $key,
                  'products' => []
              ];
          }

          $groupedCategories[$key]['id'][] = $category->getId();

          foreach ($category->getProducts() as $product) {
              $groupedCategories[$key]['products'][] = $product;
          }
      }

      $context = ['groups' => 'category:read', 'circular_reference_handler' => function ($object) {
        return $object->getId();
      }];
      return $this->serializer->serialize(array_values($groupedCategories), 'json', $context);
    }

    public function getCategoryById(int $id): ?array
    {
      $category = $this->categoryRepository->find($id);
      if (!$category) {
          return null;
      }

      $context = ['groups' => 'category:read', 'circular_reference_handler' => function ($object) {
        return $object->getId();
      }];

      $data = $this->serializer->serialize($category, 'json', $context);
      return ['data' => $data];
    }

    public function createCategory(string $data): Category
    {
        $context = ['groups' => ['category:write', 'category:read']];
        $category = $this->serializer->deserialize($data, Category::class, 'json', $context);

        $errors = $this->validator->validate($category);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $violation) {
                $errorMessages[] = $violation->getMessage();
            }
            throw new \Exception(json_encode($errorMessages));
        }

        foreach ($category->getProducts() as $product) {
            foreach ($product->getEANCodes() as $eanCode) {
                $eanCode->setProduct($product);
            }
        }

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    public function updateCategory(int $id, string $content)
    {
      $category = $this->entityManager->getRepository(Category::class)->find($id);

      if (!$category) {
          throw new \Exception('Category not found');
      }

      $context = ['groups' => ['category:write', 'category:read'], 'object_to_populate' => $category];
      $category = $this->serializer->deserialize($content, Category::class, 'json', $context);

      $errors = $this->validator->validate($category);
      if (count($errors) > 0) {
        $errorMessages = [];
        foreach ($errors as $violation) {
            $errorMessages[] = $violation->getMessage();
        }
        throw new \Exception(json_encode($errorMessages));
      }

      foreach ($category->getProducts() as $product) {
          foreach ($product->getEANCodes() as $eanCode) {
              $eanCode->setProduct($product);
          }
      }

      $this->entityManager->persist($category);
      $this->entityManager->flush();

      return $category;
    }

    public function deleteCategory(int $id): ?Category
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) {
            return null;
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $category;
    }

    public function serializeCategory(Category $category): string
    {
      $context = ['groups' => ['category:read']];
      return $this->serializer->serialize($category, 'json', $context);
    }
}
