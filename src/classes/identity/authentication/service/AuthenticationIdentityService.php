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
     * @throws DatabaseConnectionException
     */
    public static function register(string $email, string $password): bool
    {
        if (!PasswordStrengthCheckerService::check($password)) {
            throw new AuthenticationException("<p>password trop faible</p>");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
        try {
            $db = ConnectionFactory::getConnection();
        } catch (DatabaseConnectionException $e) {
            throw new DatabaseConnectionException("<p>Erreur de connexion à la base de données</p>");
        }

        $query_email = $db->prepare('SELECT id FROM user WHERE email = :email');
        $query_email->execute([':email' => $email]);
        if ($query_email->fetch()) {
            throw new AuthenticationException("<p>Cet email est déjà utilisé</p>");
        }

        try {
            $query = $db->prepare('INSERT INTO user (email, passwd, role) VALUES (:email, :passwd, :role)');
            $query->execute([':email' => $email, ':passwd' => $hash, ':role' => 1]);
        } catch (\PDOException $e) {
            throw new DatabaseConnectionException("<p>Erreur d'insertion dans la base de données</p>");
        }

        return true;
    }
}