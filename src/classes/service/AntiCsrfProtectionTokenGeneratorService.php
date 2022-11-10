<?php

namespace Application\service;

class AntiCsrfProtectionTokenGeneratorService
{
    /**
     * @throws \Exception
     */
    public function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
