<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/products")
 */

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'manufacturer' => $product->getManufacturer(),
                'price' => $product->getPrice(),
                // Add more fields as needed
            ];
        }
        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function getOne(int $id): JsonResponse
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }
        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'manufacturer' => $product->getManufacturer(),
            'price' => $product->getPrice(),
            // Add more fields as needed
        ];
        return new JsonResponse($data);
    }

    /**
     * @Route("/", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setManufacturer($data['manufacturer']);
        $product->setPrice($data['price']);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return new JsonResponse(['id' => $product->getId()], Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     */
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

    /**
     * @Route("/{id}", methods={"DELETE"})
     */
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
