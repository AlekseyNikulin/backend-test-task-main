<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Manager;

use Exception;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Core\Exception\ConnectorException;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Infrastructure\ConnectorFacade;

class CartManager extends ConnectorFacade
{
    public ?LoggerInterface $logger = null;

    public function __construct(string $host, int $port, ?string $password)
    {
        parent::__construct(
            host: $host,
            port: $port,
            password: $password,
            dbIndex: 1,
        );
        parent::build();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function saveCart(Cart $cart): void
    {
        try {
            $this->connector->set(
                key: session_id(),
                value: $cart,
            );
        } catch (Exception|ConnectorException $e) {
            $this->logger?->error(sprintf('Error: %s', $e->getMessage()));
        }
    }

    /**
     * @return ?Cart
     */
    public function getCart(): ?Cart
    {
        try {
            return $this->connector->get(key: session_id());
        } catch (Exception|ConnectorException $e) {
            $this->logger?->error(sprintf('Error: %s', $e->getMessage()));
        }

        return null;
    }
}
