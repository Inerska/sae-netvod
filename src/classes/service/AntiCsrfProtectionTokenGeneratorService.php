<?php

namespace Application\service;

class AntiCsrfProtectionTokenGeneratorService
{
    /**
     * @throws \Exception
     */
    private function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function generateTokenTime() : int
    {
        return time();
    }

    private function generateTokenAndTime() : array
    {
        return [
            'token' => $this->generateToken(),
            'time' => $this->generateTokenTime()
        ];
    }

    public function protect() : string
    {
        $tokenAndTime = $this->generateTokenAndTime();
        $_SESSION['token'] = $tokenAndTime['token'];
        $_SESSION['token_time'] = $tokenAndTime['time'];

        return $tokenAndTime['token'];
    }

    public function verify(string $token, int $delay) : bool
    {
        return $token === $_SESSION['token'] && $this->generateTokenTime() - $_SESSION['token_time'] < $delay;
    }
}
