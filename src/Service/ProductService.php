<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager, 
        private Security $security
    ) {
    }

    public function getProducts()
    {
        return $this->productRepository->findAll();
    }

    public function getProductById($id)
    {
        return $this->productRepository->find($id);
    }

    public function createProduct(Product $product)
    {   
        $product->setOwner($this->security->getUser());
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return ['success' => 'Product created successfully'];
    }

    public function updateProduct(Product $existingProduct, Product $updatedProduct)
    {
        $existingProduct->setName($updatedProduct->getName());
        $existingProduct->setDescription($updatedProduct->getDescription());
        $existingProduct->setPrice($updatedProduct->getPrice());
        $existingProduct->setQuantity($updatedProduct->getQuantity());

        $this->entityManager->flush();

        return ['success' => 'Product updated successfully'];
    }

    public function deleteProduct(Product $product)
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return ['success' => 'Product deleted successfully'];
    }
}
