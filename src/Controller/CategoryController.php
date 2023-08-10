<?php
namespace App\Controller;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\EANCode;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/categories')]
class CategoryController extends AbstractController
{
    private $categoryRepository;
    private $entityManager;
    private $serializer;
    private $categoryService;

    public function __construct(CategoryService $categoryService, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->categoryService = $categoryService;
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
      $groupedCategories = $this->categoryService->getGroupedCategories();
      $context = ['groups' => 'category:read', 'circular_reference_handler' => function ($object) {
          return $object->getId();
      }];
      $categories = $this->serializer->serialize($groupedCategories, 'json', $context);
      return new JsonResponse($categories, 200, [], true);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show($id): JsonResponse
    {
      $category = $this->categoryService->getCategoryById($id);
      if (!$category) { return new JsonResponse(['error' => 'Category not found'], 404);}
      return new JsonResponse($category['data'], 200, [], true);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
      $data = $request->getContent();
      $category = $this->categoryService->createCategory($data);
      return new JsonResponse(['status' => 'Category created'], 201);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update($id, Request $request): JsonResponse
    {
      try {
        $data = $request->getContent();
        $this->categoryService->updateCategory($id, $data);
        return new JsonResponse(['status' => 'Category updated successfully'], 200);
      } catch (\Exception $e) {
        return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
      }
      // $category = $this->entityManager->getRepository(Category::class)->find($id);
      // if (!$category) {
      //     return $this->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
      // }

      // $context = ['groups' => ['category:write'], 'object_to_populate' => $category];
      // $category = $this->serializer->deserialize($request->getContent(), Category::class, 'json', $context);

      // foreach ($category->getProducts() as $product) {
      //   foreach ($product->getEANCodes() as $eanCode) {
      //     $eanCode->setProduct($product);
      //   }
      // }

      // $this->entityManager->persist($category);
      // $this->entityManager->flush();

      // return new JsonResponse(['status' => 'Category updated successfully'], 200);
    }

    #[Route("/{id}", methods: ["DELETE"])]
    public function delete($id): JsonResponse
    {
      $category = $this->categoryService->deleteCategory($id);
      if (!$category) { return new JsonResponse(['error' => 'Category not found'], 404);}
      return new JsonResponse(['status' => 'Category deleted'], 200);
    }
}

    // #[Route('', methods: ['GET'])]
    // public function index(): JsonResponse
    // {
    //   $categories = $this->categoryRepository->findAll();
    //   $context = ['groups' => 'category:read', 'circular_reference_handler' => function ($object) {
    //     return $object->getId();
    //   }];
    //   $data = $this->serializer->serialize($categories, 'json', $context);
    //   return new JsonResponse($data, 200, [], true);
    // }

    // Previously used for the POST method
    // $data = $request->getContent();
    // $context = ['groups' => ['category:write', 'category:read']];
    // $category = $this->serializer->deserialize($data, Category::class, 'json', $context);

    // foreach ($category->getProducts() as $product) {
    //     foreach ($product->getEANCodes() as $eanCode) {
    //         $eanCode->setProduct($product);
    //     }
    // }

    // $this->entityManager->persist($category);
    // $this->entityManager->flush();

    // return new JsonResponse(['status' => 'Category created'], 201);
