<?php
namespace App\Controller;
use App\Service\ProductService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/products')]
class ProductController extends AbstractController
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
      $serializedProducts = $this->productService->getCategorizedProducts();
      return new JsonResponse($serializedProducts, 200, [], true);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show($id): JsonResponse
    {
      $product = $this->productService->getProductById($id);
      if (!$product) { return new JsonResponse(['error' => 'Product not found'], 404);}
      return new JsonResponse($product, 200, [], true);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
      $data = $request->getContent();
      try {
        $product = $this->productService->createProduct($data);
        return new JsonResponse(['status' => 'Product created', 'product' => json_decode($product)], 200);
      } catch (\Exception $e) {
        return new JsonResponse(['error' => json_decode($e->getMessage())], 400);
      }
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update($id, Request $request): JsonResponse
    {
      $data = $request->getContent();
      try {
          $product = $this->productService->updateProduct($id, $data);
          return new JsonResponse(['status' => 'Product updated', 'product' => json_decode($product)], 200);
      } catch (\Exception $e) {
          return new JsonResponse(['error' => $e->getMessage()], 400);
      }
    }

    #[Route("/{id}", methods: ["DELETE"])]
    public function delete($id): JsonResponse
    {
      $product = $this->productService->deleteProduct($id);
      if (!$product) { return new JsonResponse(['error' => 'Product not found'], 404);}
      return new JsonResponse(['status' => 'Product deleted'], 200);
    }
}
