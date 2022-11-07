<?php

declare(strict_types=1);

namespace Application\identity\authentication\service;

use Application\datalayer\factory\ConnectionFactory;
use Application\exception\datalayer\DatabaseConnectionException;
use Application\exception\identity\AuthenticationException;
use Application\exception\identity\BadPasswordException;
use Application\identity\model\User;

class AuthenticationIdentityService
{
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

        return new User($row['id'], $email);
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