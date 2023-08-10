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

#[Route('/api/category_products')]
class CategoryAndProductController extends AbstractController
{
    private $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
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
          return new JsonResponse(['status' => 'Category&Product created', 'category' => json_decode($serializedCategory)], 201);
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
}
