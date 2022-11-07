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
                    <input type="email" name="email" placeholder="Adresse mail" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="submit" value="S'enregistrer">
                </form>
            END;
        }

        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];

        try {
            AuthenticationIdentityService::register($email, $password);
        } catch (DatabaseConnectionException|AuthenticationException $e) {
            return $e->getMessage();
        }

        return 'You have been signed up!';
    }
}