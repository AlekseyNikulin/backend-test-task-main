<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Interface;

interface ViewInterface
{
    public function get(array $data): array;

    public function create(array $data): array;
}