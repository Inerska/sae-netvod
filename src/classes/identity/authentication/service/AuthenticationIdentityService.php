<?php

declare(strict_types=1);

namespace Application\identity\authentication\service;

use Application\datalayer\factory\ConnectionFactory;
use Application\exception\datalayer\DatabaseConnectionException;
use Application\exception\identity\AuthenticationException;

class AuthenticationIdentityService
{
    /**
     * @throws AuthenticationException
     * @throws DatabaseConnectionException
     */
    public static function authenticate(string $email, string $password): bool
    {
        $query = "select * from user where email = ?";
        $context = ConnectionFactory::getConnection();

        $statement = $context->prepare($query);
        $result = $context->execute([$email]);

        if (!$result) {
            throw new AuthenticationException("Authentication failed");
        }

        $user = $statement->fetch();

        if (!password_verify($password, $user['password'])) {
            throw new AuthenticationException("Authentication failed");
        }

        return true;
    }

    /**
     * @throws AuthenticationException
     */
    public static function register(string $email, string $password): bool
    {
        //TODO
        throw new AuthenticationException("Not implemented");
    }
}