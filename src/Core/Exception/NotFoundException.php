<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Core\Exception;

use Throwable;

class NotFoundException extends BackendHttpException
{
    public function __construct(string $message = "Not found", int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}