<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Raketa\BackendTestTask\Entity\Product;

class ProductRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function getByUuid(string $uuid): ?Product
    {
        $uuid = trim($uuid);

        if (!$uuid) {
            return null;
        }

        $row = $this->connection->fetchOne(
            query: /* @lang SQL */ 'SELECT * FROM products WHERE uuid = :uuid',
            params: [
                'uuid' => $uuid,
            ],
        );

        return $row ? $this->make($row) : null;
    }

    public function getByCategory(string $category): array
    {
        $category = trim($category);

        if (!$category) {
            return [];
        }

        return array_map(
            static fn (array $row): Product => $this->make(row: $row),
            $this->connection->fetchAllAssociative(
                query: /* @lang SQL */ 'SELECT * FROM products WHERE is_active = 1 AND category = :category',
                params: [
                    'category' => $category,
                ],
            )
        );
    }

    public function make(array $row): Product
    {
        return new Product(
            $row['id'],
            $row['uuid'],
            $row['is_active'],
            $row['category'],
            $row['name'],
            $row['description'],
            $row['thumbnail'],
            $row['price'],
        );
    }
}
