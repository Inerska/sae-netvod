<?php

namespace Application\action;

use Application\exception\datalayer\DatabaseConnectionException;
use Application\exception\identity\AuthenticationException;
use Application\identity\authentication\service\AuthenticationIdentityService;

class SignupAction extends Action
{
    final public function execute(): string
    {
        if ($this->httpMethod === 'GET') {
            return <<<END
                <form method="POST">
                    <input type="email" name="email" poncopy="return false" onpaste="return false" placeholder="Adresse mail" required>
                    <input type="password" name="password" oncopy="return false" onpaste="return false" placeholder="Password" required>
                    <input type="password" name="confirm" oncopy="return false" onpaste="return false" placeholder="Confirm" required>
                    <input type="submit" value="S'enregistrer">
                </form>
            END;
        }

        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];

        try {
            $token = AuthenticationIdentityService::register($email, $password, $confirm);
        } catch (DatabaseConnectionException|AuthenticationException $e) {
            return $e->getMessage();
        }

        return <<<END
            <p>Compte enregister avec succès, veuillez activer votre compte</p>
            <a href="index.php?action=activation&token={$token}">Activer mon compte</a>
        END;

    }
}