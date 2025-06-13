<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Core\Controller\BackendController;
use Raketa\BackendTestTask\View\ProductsView;

class ProductController extends BackendController
{
    public function __construct(
        private readonly ProductsView $productsVew
    ) {
    }

    public function get(RequestInterface $request): ResponseInterface
    {
        return $this->response(
            data: $this->productsVew->get(
                data: $this->fromParams($request),
            ),
        );
    }
}
