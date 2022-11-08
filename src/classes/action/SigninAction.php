<?php

namespace Application\action;

use Application\exception\identity\AuthenticationException;
use Application\exception\identity\BadPasswordException;
use Application\identity\authentication\service\AuthenticationIdentityService;

class SigninAction extends Action
{

    public function execute(): string
    {

        $html = <<<END
            <form method="post" action="?action=sign-in">
                  <input type="email" name="email" placeholder="votre email">
                  <input type="text" name="password" placeholder="votre mdp">
                  <button type="submit">Connexion</button>
            </form>
            <a href="index.php?action=renew">Mot de passe oublié ?</a>
        END;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS);


            try {
                $user = AuthenticationIdentityService::authenticate($email, $password);
                $html .= "<p>Connexion réussi</p>";
                $html .= "<p>Bienvenue $email</p>";
                $_SESSION['loggedUser'] = serialize($user);
            } catch (AuthenticationException $e) {
                $html .= "<p>Compte inexistant</p>";
            } catch (BadPasswordException $e) {
                $html .= "<p>Mot de passe incorrect</p>";
            }

        }

        $html .= "<br><br><a href='index.php'>Retour page principale</a>";
        return $html;

    }
}