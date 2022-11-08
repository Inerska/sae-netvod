<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\exception\identity\AuthenticationException;
use Application\exception\identity\BadPasswordException;
use Application\identity\authentication\service\AuthenticationIdentityService;

class SigninAction extends Action
{

    public function execute(): string
    {

        $html = <<<END
            <form method="post" action="?action=sign-in">
                  <input type="email" name="email" oncopy="return false" onpaste="return false" placeholder="votre email">
                  <input type="password" name="password" oncopy="return false" onpaste="return false" placeholder="votre mdp">
                  <button type="submit" class="bg-blue-500 rounded p-3 text-white hover:bg-blue-600">Connexion</button>
            </form>
            <a href="index.php?action=renew" class="text-gray-900 dark:text-white">Mot de passe oublié ?</a>
        END;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS);

            try {
                $user = AuthenticationIdentityService::authenticate($email, $password);

                $db = ConnectionFactory::getConnection();

                $query = $db->prepare("SELECT * FROM user WHERE email = ?");
                $query->execute([$email]);
                $result = $query->fetch();

                if ((int)$result['active'] === 0) {
                    $html = "<p class='text-gray-900 dark:text-white'>Compte non activé</p>";
                } else {
                    $html .= "<p class='text-gray-900 dark:text-white'>Connexion réussi</p>";
                    $html .= "<p class='text-gray-900 dark:text-white'>Bienvenue $email</p>";
                    $_SESSION['loggedUser'] = serialize($user);

                    header("Location: index.php");
                    exit();
                }


            } catch (AuthenticationException $e) {
                $html .= "<p class='text-gray-900 dark:text-white'>Compte inexistant</p>";
            } catch (BadPasswordException $e) {
                $html .= "<p class='text-gray-900 dark:text-white'>Mot de passe incorrect</p>";
            }

        }

        $html .= "<br><br><a href='index.php' class='text-gray-900 dark:text-white'>Retour page principale</a>";

        return $html;

    }
}