<?php

declare(strict_types=1);

namespace Application\identity\authentication\service;

use Application\datalayer\factory\ConnectionFactory;
use Application\exception\datalayer\DatabaseConnectionException;
use Application\exception\identity\AuthenticationException;
use Application\identity\model\User;
use PDOException;

class AuthenticationIdentityService
{

    private const AUTHENTICATION_FAIL_ERROR_MESSAGE = 'Authentication failed';

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
            throw new AuthenticationException(self::AUTHENTICATION_FAIL_ERROR_MESSAGE);
        }

        $user = $statement->fetch();

        if (!password_verify($password, $user['password'])) {
            throw new AuthenticationException(self::AUTHENTICATION_FAIL_ERROR_MESSAGE);
        }

        return true;
    }

    /**
     * @throws AuthenticationException
     * @throws DatabaseConnectionException
     */
    public static function register(string $email, string $password, string $confirm): bool
    {
        if ($password !== $confirm) {
            throw new AuthenticationException("Passwords do not match");
        }

        if (!PasswordStrengthCheckerService::check($password)) {
            throw new AuthenticationException("<p>password trop faible</p>");
        }

        if (self::alreadyExists($email)) {
            throw new AuthenticationException("<p>Cet email est déjà utilisé</p>");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

        try {
            $db = ConnectionFactory::getConnection();
        } catch (DatabaseConnectionException $e) {
            throw new DatabaseConnectionException("<p>Erreur de connexion à la base de données</p>");
        }

        try {
            $query = $db->prepare('INSERT INTO user (email, passwrd, role) VALUES (:email, :passwrd, :role)');
            $query->execute([':email' => $email, ':passwrd' => $hash, ':role' => 1]);

            $_SESSION['loggedUser'] = serialize(new User((int)$db->lastInsertId(), $email, $password));
        } catch (PDOException $e) {
            throw new DatabaseConnectionException("<p>Erreur d'insertion dans la base de données</p> : " . $e->getMessage());
        }

        return true;
    }

    /**
     * @throws AuthenticationException
     * @throws DatabaseConnectionException
     */
    public static function alreadyExists(string $email): bool
    {
        $query = "select * from user where email = ?";
        $context = ConnectionFactory::getConnection();

        $statement = $context->prepare($query);

        $result = $statement->execute([$email]);

        if (!$result) {
            throw new AuthenticationException("Authentication failed");
        }

        return $statement->fetch();
    }
}