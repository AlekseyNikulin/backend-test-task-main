<?php

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Core\Controller\BackendController;
use Raketa\BackendTestTask\View\CartView;

class CartController extends BackendController
{
    public function __construct(
        private readonly CartView $cartView,
    ) {
    }

    public function view(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->response(
                data: $this->cartView->get(
                    data: $this->fromParams($request),
                ),
            );
        } catch (\Throwable $e) {
            return $this->responseError(
                message: $e->getMessage(),
                code: $e->getCode(),
            );
        }
    }

    public function create(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->response(
                data: $this->cartView->create(
                    data: $this->fromRequest($request),
                ),
            );
        } catch (\Throwable $e) {
            return $this->responseError(
                message: $e->getMessage(),
                code: $e->getCode(),
            );
        }
    }
}
