<?php

declare(strict_types=1);

namespace Application\identity\authentication\service;

use Application\datalayer\factory\ConnectionFactory;
use Application\exception\datalayer\DatabaseConnectionException;
use Application\exception\identity\AuthenticationException;

use Application\exception\identity\BadPasswordException;
use Application\identity\model\User;
use PDOException;


class AuthenticationIdentityService
{

    private const AUTHENTICATION_FAIL_ERROR_MESSAGE = 'Authentication failed';

    /**
     * @throws AuthenticationException
     * @throws DatabaseConnectionException
     * @throws BadPasswordException
     */
    public static function authenticate(string $email, string $password): User
    {

        $db = ConnectionFactory::getConnection();
        $st = $db->prepare("select * from user where email = ?");
        $st->execute([$email]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        // s'il n'y a pas de retour à la requetes, c'est que l'utilisateur n'existe pas
        if(! $row){
            throw new AuthenticationException("erreur d'auth");

        }

        $hash = $row['passwrd'];


        // si ce n'est pas le bon password
        if (! password_verify($password, $hash)){
            throw new BadPasswordException();
        }

        return new User((int)$row['id'], $email);
    }

    /**
     * @throws AuthenticationException
     * @throws DatabaseConnectionException
     */
    public static function register(string $email, string $password, string $confirm): string
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
            $query = $db->prepare('INSERT INTO user (email, passwrd, role, active, activationToken, expirationToken) VALUES (:email, :passwrd, :role, false, :token, :expiration)');
            $token = bin2hex(random_bytes(32));
            $expiration = time() + 60;
            $query->execute([':email' => $email, ':passwrd' => $hash, ':role' => 1, ':token' => $token, ':expiration' => $expiration]);

        } catch (PDOException $e) {
            throw new DatabaseConnectionException("<p>Erreur d'insertion dans la base de données</p> : " . $e->getMessage());
        }

        return $token;
    }


    public static function regenerateToken(int $id) : string {
        $token = bin2hex(random_bytes(32));
        $expiration = time() + 60;
        $db = ConnectionFactory::getConnection();
        $query = $db->prepare('UPDATE user SET activationToken = :token, expirationToken = :expiration WHERE id = :id');
        $query->execute([':token' => $token, ':expiration' => $expiration, ':id' => $id]);
        return $token;
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