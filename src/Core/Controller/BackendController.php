<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Core\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Core\Response\JsonResponse;
use Raketa\BackendTestTask\Enum\ResponseStatusEnum;

class BackendController
{
    protected function response(array $data, int $code = 200): ResponseInterface
    {
        $response = $this->getJsonResponse($code);
        $response->getBody()->write(
            json_encode(
                value: array_merge(
                    [
                        'status' => ResponseStatusEnum::tryFrom($code)->label(),
                    ],
                    $data,
                ),
                flags: JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
            )
        );

        return $response;
    }

    protected function responseError(string $message, int $code = 500): ResponseInterface
    {
        $response = $this->getJsonResponse($code);
        $response->getBody()->write(
            json_encode(
                value: [
                    'status' => ResponseStatusEnum::tryFrom($code)->label(),
                    'message' => $message,
                ],
                flags: JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
            )
        );

        return $response;
    }

    protected function fromParams(RequestInterface $request): ?array
    {
        parse_str(
            string: $request->getUri()->getQuery(),
            result: $params,
        );

        return $params ?: null;
    }

    protected function fromRequest(RequestInterface $request): ?array
    {
        return json_decode(
            json: $request->getBody()->getContents(),
            associative: true,
        );
    }

    private function getJsonResponse(int $code = 200): ResponseInterface
    {
        return (new JsonResponse())->withHeader(
            name: 'Content-Type',
            value: 'application/json; charset=utf-8',
        )
            ->withStatus($code);
    }
}