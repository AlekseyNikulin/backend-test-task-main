<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Service;

use Raketa\BackendTestTask\Core\Exception\NotFoundException;
use Raketa\BackendTestTask\Entity\Product;
use Raketa\BackendTestTask\Interface\ViewInterface;
use Raketa\BackendTestTask\Repository\ProductRepository;

readonly class ProductService implements ViewInterface
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function getByUuid(string $uuid): Product
    {
        $product = $this->productRepository->getByUuid($uuid);

        if (!$product) {
            throw new NotFoundException('Product not found');
        }

        return $product;
    }

    public function get(array $data): array
    {
        return [];
    }

    public function create(array $data): array
    {
        return [];
    }

    public function getByCategory(string $category): array
    {
        return $this->productRepository->getByCategory($category);
    }
}