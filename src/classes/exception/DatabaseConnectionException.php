<?php

declare(strict_types=1);

namespace Application\exception;

use Exception;
use Throwable;

class DatabaseConnectionException extends Exception
{
    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}