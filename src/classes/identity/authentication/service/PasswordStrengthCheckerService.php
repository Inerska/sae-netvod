<?php

declare(strict_types=1);

namespace Application\identity\authentication\service;

class PasswordStrengthCheckerService
{
    public const PASSWORD_MINIMAL_LENGTH = 8;
    public const HAS_UPPERCASE = '/[A-Z]/';
    public const HAS_LOWERCASE = '/[a-z]/';
    public const HAS_NUMBER = '/\d/';
    public const HAS_SPECIAL_CHARACTERS = '/\W/';

    public static function check(string $password): bool {
        return strlen($password) >= self::PASSWORD_MINIMAL_LENGTH
            && preg_match(self::HAS_UPPERCASE, $password)
            && preg_match(self::HAS_LOWERCASE, $password)
            && preg_match(self::HAS_NUMBER, $password)
            && preg_match(self::HAS_SPECIAL_CHARACTERS, $password);
    }
}