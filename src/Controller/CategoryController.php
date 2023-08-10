<?php
namespace App\Controller;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\EANCode;
use App\Service\CategoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/category_products')]
class CategoryController extends AbstractController
{
    private $categoryService;
    private $serializer;
    private $entityManager;
    private $validator;

    public function __construct(CategoryService $categoryService, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->categoryService = $categoryService;

        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
      $serializedCategories = $this->categoryService->getGroupedCategories();
      return new JsonResponse($serializedCategories, 200, [], true);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show($id): JsonResponse
    {
      $category = $this->categoryService->getCategoryById($id);
      if (!$category) { return new JsonResponse(['error' => 'Category&Product not found'], 404);}
      return new JsonResponse($category['data'], 200, [], true);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
      $data = $request->getContent();
      try {
          $category = $this->categoryService->createCategory($data);
          $serializedCategory = $this->categoryService->serializeCategory($category);
          return new JsonResponse(['status' => 'Category&Product created', 'product' => json_decode($serializedCategory)], 201);
      } catch (\Exception $e) {
          return new JsonResponse(['error' => json_decode($e->getMessage())], 400);
      }
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update($id, Request $request): JsonResponse
    {
      $data = $request->getContent();
      try {
          $category = $this->categoryService->updateCategory($id, $data);
          $serializedCategory = $this->categoryService->serializeCategory($category);
          return new JsonResponse(['status' => 'Category&Product updated', 'category' => json_decode($serializedCategory)], 201);
      } catch (\Exception $e) {
          return new JsonResponse(['error' => json_decode($e->getMessage())], 400);
      }
    }

    #[Route("/{id}", methods: ["DELETE"])]
    public function delete($id): JsonResponse
    {
      $category = $this->categoryService->deleteCategory($id);
      if (!$category) { return new JsonResponse(['error' => 'Category not found'], 404);}
      return new JsonResponse(['status' => 'Category&Product deleted'], 200);
    }


    //Test to see if its better for products to have many categories and categories to belong to products. A many many relation
    #[Route('/test_product', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $data = $request->getContent();
        $context = ['groups' => ['category:write', 'category:read', 'product:read', 'product:write']];
        $product = $this->serializer->deserialize($data, Product::class, 'json', $context);
        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $violation) {
                $errorMessages[] = $violation->getMessage();
            }
            return new JsonResponse(['error' => $errorMessages], 400);
        }

        foreach ($product->getEANCodes() as $eanCode) {
            $eanCode->setProduct($product);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $serializedProduct = $this->categoryService->serializeProduct($product);

        return new JsonResponse(['status' => 'Category & Product created', 'product' => json_decode($serializedProduct)], 201);
    }
}
