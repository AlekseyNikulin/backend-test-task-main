<?php

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Entity\Product;
use Raketa\BackendTestTask\Interface\ViewInterface;
use Raketa\BackendTestTask\Service\ProductService;

readonly class ProductsView implements ViewInterface
{
    public function __construct(
        private ProductService $productService,
    ) {
    }

    /**
     * @param Product[] $products
     * @return array<int, array>
     */
    public function toArray(array $products): array
    {
        return array_map(
            fn (Product $product) => [
                'id' => $product->getId(),
                'uuid' => $product->getUuid(),
                'category' => $product->getCategory(),
                'description' => $product->getDescription(),
                'thumbnail' => $product->getThumbnail(),
                'price' => $product->getPrice(),
            ],
            $products,
        );
    }

    /**
     * @param array $data
     * @return Product[]
     */
    public function get(array $data): array
    {
        return $this->toArray(
            products: $this->productService->getByCategory(
                category: $data['category'] ?? '',
            ),
        );
    }

    public function create(array $data): array
    {
        return [];
    }
}
