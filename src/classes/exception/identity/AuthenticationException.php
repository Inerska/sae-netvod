<?php

declare(strict_types=1);

namespace Application\exception\identity;

use Exception;
use Throwable;

class AuthenticationException extends Exception
{
    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}