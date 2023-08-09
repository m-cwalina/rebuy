<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\EANCode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/products')]
class ProductController extends AbstractController
{
    private $entityManager;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

#[Route('', methods: ['GET'])]
public function index(): JsonResponse
{
  $products = $this->entityManager->getRepository(Product::class)->findAll();
  $context = ['groups' => 'product:read', 'circular_reference_handler' => function ($object) {
    return $object->getId();
  }];
  $data = $this->serializer->serialize($products, 'json', $context);

  return new JsonResponse($data, 200, [], true);
}

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }
        $context = ['groups' => 'product:read', 'circular_reference_handler' => function ($object) {
          return $object->getId();
        }];
        $data = $this->serializer->serialize($product, 'json', $context);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    $product = new Product();
    $product->setName($data['name']);
    $product->setManufacturer($data['manufacturer']);
    $product->setPrice($data['price']);

    foreach ($data['categories'] as $categoryName) {
        $category = new Category();
        $category->setCategory($categoryName);
        $category->setProduct($product);
        $product->addCategory($category);

        $this->entityManager->persist($category);
    }

    foreach ($data['eanCodes'] as $codeValue) {
        $eanCode = new EANCode();
        $eanCode->setCode($codeValue);
        $eanCode->setProduct($product);
        $product->addEANCode($eanCode);

        $this->entityManager->persist($eanCode);
    }

    $this->entityManager->persist($product);
    $this->entityManager->flush();

    return new JsonResponse(['id' => $product->getId()], Response::HTTP_CREATED);
}

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $product->setName($data['name']);
        $product->setManufacturer($data['manufacturer']);
        $product->setPrice($data['price']);

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Product updated']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Product deleted']);
    }
}
