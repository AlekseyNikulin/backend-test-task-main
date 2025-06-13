<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Core\Exception\NotFoundException;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Interface\ViewInterface;
use Raketa\BackendTestTask\Service\CartService;

readonly class CartView implements ViewInterface
{
    public function __construct(
        private CartService $cartService,
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function toArray(Cart $cart): array
    {
        $data = [
            'uuid' => $cart->getUuid(),
            'customer' => [
                'id' => $cart->getCustomer()->getId(),
                'name' => implode(
                    ' ',
                    array_filter([
                        $cart->getCustomer()->getLastName(),
                        $cart->getCustomer()->getFirstName(),
                        $cart->getCustomer()->getMiddleName(),
                    ]),
                ),
                'email' => $cart->getCustomer()->getEmail(),
            ],
            'payment_method' => $cart->getPaymentMethod(),
        ];

        $total = 0;
        $data['items'] = [];
        foreach ($cart->getItems() as $item) {
            $total += $item->getPrice() * $item->getQuantity();
            $product = $this->cartService->getProductByUuid($item->getProductUuid());

            $data['items'][] = [
                'uuid' => $item->getUuid(),
                'price' => $item->getPrice(),
                'total' => $total,
                'quantity' => $item->getQuantity(),
                'product' => [
                    'id' => $product->getId(),
                    'uuid' => $product->getUuid(),
                    'name' => $product->getName(),
                    'thumbnail' => $product->getThumbnail(),
                    'price' => $product->getPrice(),
                ],
            ];
        }

        $data['total'] = $total;

        return $data;
    }

    /**
     * @return array{
     *     cart: array
     * }
     * @throws NotFoundException
     */
    public function get(array $data): array
    {
        $cart = $this->cartService->get($data);

        return [
            'cart' => $this->toArray($cart),
        ];
    }

    /**
     * @return array{
     *     cart: array
     * }
     * @throws NotFoundException
     */
    public function create(array $data): array
    {
        $cart = $this->cartService->create($data);

        return [
            'cart' => $this->toArray($cart),
        ];
    }
}
