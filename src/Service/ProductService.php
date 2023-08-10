<?php

namespace App\Service;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\EANCode;
use App\Repository\ProductRepository;

class ProductService
{
    private $categoryRepository;
    private $entityManager;
    private $serializer;
    private $validator;
    private $productRepository;

    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator, ProductRepository $productRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function getCategorizedProducts(): string
    {
        $products = $this->productRepository->findAll();

        $categorizedProducts = [];
        foreach ($products as $product) {
            foreach ($product->getCategories() as $category) {
                $categoryName = $category->getCategory();
                $categorizedProducts[$categoryName]['category'] = $categoryName;
                $categorizedProducts[$categoryName]['products'][] = $product;
            }
        }

        $context = [
            'groups' => ['product:read', 'product:write', 'category:read', 'category:write'],
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ];

        return $this->serializer->serialize($categorizedProducts, 'json', $context);
    }

    public function getProductById(int $id): string
    {
      $product = $this->productRepository->find($id);
      if (!$product) {
          return null;
      }

      $context = ['groups' => ['category:read', 'category:write', 'product:read', 'product:write'], 'circular_reference_handler' => function ($object) {
        return $object->getId();
      }];

      return $this->serializer->serialize($product, 'json', $context);
    }

    public function createProduct(string $data): String
    {
      $context = ['groups' => ['category:write', 'category:read', 'product:read', 'product:write']];
      $product = $this->serializer->deserialize($data, Product::class, 'json', $context);

      $errors = $this->validator->validate($product);
      if (count($errors) > 0) {
          $errorMessages = [];
          foreach ($errors as $violation) {
              $errorMessages[] = $violation->getMessage();
          }
          throw new \Exception(json_encode($errorMessages));
      }

      foreach ($product->getEANCodes() as $eanCode) {
          $eanCode->setProduct($product);
      }

      $this->entityManager->persist($product);
      $this->entityManager->flush();

      return $this->serializer->serialize($product, 'json', $context);
    }

    public function updateProduct(int $id, string $content): String
    {
      $product = $this->productRepository->find($id);

      if (!$product) {
          throw new \Exception('Product not found');
      }

      $context = ['groups' => ['category:write', 'category:read', 'product:write', 'product:read'], 'object_to_populate' => $product];
      $product = $this->serializer->deserialize($content, Product::class, 'json', $context);

      $errors = $this->validator->validate($product);
      if (count($errors) > 0) {
          $errorMessages = [];
          foreach ($errors as $violation) {
              $errorMessages[] = $violation->getMessage();
          }
          throw new \Exception(json_encode($errorMessages));
      }

      foreach ($product->getEANCodes() as $eanCode) {
          $eanCode->setProduct($product);
      }

      $this->entityManager->persist($product);
      $this->entityManager->flush();

      return $this->serializer->serialize($product, 'json', $context);
    }

    public function deleteProduct(int $id): ?Product
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return null;
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $product;
    }
}
