<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\UpdateProductType;
use App\Form\StoreProductType;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/products')]
class ProductController extends AbstractController
{
    public function __construct(
        private ProductService $productService,
        private SerializerInterface $serializer,
        private FormFactoryInterface $formFactory
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     security={{ "Bearer":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="List of products",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     )
     * )
     */
    #[Route('', name: 'product_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $products = $this->productService->getProducts();
        $data = $this->serializer->serialize($products, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     security={{ "Bearer":{} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="price", type="float")
     *             @OA\Property(property="quantity", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation errors",
     *         @OA\JsonContent(type="object", @OA\Property(property="errors", type="object"))
     *     )
     * )
     */
    #[Route('', name: 'product_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $product = new Product();
        $form = $this->createForm(StoreProductType::class, $product);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            $errors = $form->getErrors(true);
            $errorsString = (string) $errors;

            return new JsonResponse($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $result = $this->productService->createProduct($product);

        return new JsonResponse($result['success'], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a product by ID",
     *     security={{ "Bearer":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product details",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     )
     * )
     */
    #[Route('/{id}', name: 'product_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return new JsonResponse('Product not found', Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($product, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update a product",
     *     security={{ "Bearer":{} }},
     *     @OA\Parameter(
     *         name="     *         id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="isCompleted", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation errors",
     *         @OA\JsonContent(type="object", @OA\Property(property="errors", type="object"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    #[Route('/{id}', name: 'product_edit', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return new JsonResponse('Product not found', Response::HTTP_NOT_FOUND);
        }

        $updatedProduct = new Product();
        $form = $this->createForm(UpdateProductType::class, $updatedProduct);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            $errors = $form->getErrors(true);
            $errorsString = (string) $errors;

            return new JsonResponse($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $result = $this->productService->updateProduct($product, $updatedProduct);

        return new JsonResponse($result['success'], Response::HTTP_OK);
    }


    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     security={{ "Bearer":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Product deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    #[Route('/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return new JsonResponse('Product not found', Response::HTTP_NOT_FOUND);
        }

        $result = $this->productService->deleteProduct($product);

        return new JsonResponse($result['success'], Response::HTTP_OK);
    }
}
