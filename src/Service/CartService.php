<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Service;

use Raketa\BackendTestTask\Core\Exception\NotFoundException;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\CartItem;
use Raketa\BackendTestTask\Entity\Product;
use Raketa\BackendTestTask\Manager\CartManager;
use Ramsey\Uuid\Uuid;

readonly class CartService
{
    public function __construct(
        private ProductService $productService,
        private CartManager $cartManager,
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function get(array $data): Cart
    {
        $cart = $this->cartManager->getCart();

        if (!$cart) {
            throw new NotFoundException('Cart not found');
        }

        return $cart;
    }

    /**
     * @throws NotFoundException
     */
    public function create(array $data): Cart
    {
        $product = $this->getProductByUuid(
            uuid: $data['productUuid'] ?? '',
        );
        $cart = $this->cartManager->getCart();
        $cart->addItem(
            item: new CartItem(
                uuid: Uuid::uuid4()->toString(),
                productUuid: $product->getUuid(),
                price: $product->getPrice(),
                quantity: $data['quantity'] ?? 0,
            )
        );

        $this->cartManager->saveCart(cart: $cart);


        return $cart;
    }

    /**
     * @throws NotFoundException
     */
    public function getProductByUuid(string $uuid): Product
    {
        return $this->productService->getByUuid($uuid);
    }
}