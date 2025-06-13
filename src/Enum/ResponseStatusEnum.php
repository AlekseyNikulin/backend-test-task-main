<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Enum;

enum ResponseStatusEnum: int
{
    case Success = 200;
    case Created = 201;
    case NotFound = 404;
    case Error = 500;

    public function label(): string {
        return match ($this) {
            self::Success => 'Success',
            self::Created => 'Created',
            self::NotFound => 'Not found',
            default => 'Error',
        };
    }

}
