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
        <div class="flex justify-center items-center h-screen pb-72">
            <form method="post" action="?action=sign-in" class="bg-gray-50 dark:bg-gray-700 p-10 flex items-center justify-center flex-col">
                <div class="">
                    <h1 class="text-dark text-4xl font-sans font-semibold pb-5 dark:text-white">Se connecter</h1>
                    <div class="flex flex-col gap-3">
                        <input class="p-2 dark:bg-gray-800 bg-gray-100 border-b-4 mb-5 dark:text-gray-100" type="email" name="email" oncopy="return false" onpaste="return false" placeholder="votre email">
                        <input class="p-2 dark:bg-gray-800 bg-gray-100 border-b-4 dark:text-gray-100" type="password" name="password" oncopy="return false" onpaste="return false" placeholder="votre mdp">
                    </div>
                    <a href="?action=renew" class="text-gray-900 dark:text-white font-sm text-xs">Mot de passe oublié ?</a>
                </div>
                <button type="submit" class="bg-blue-500 p-3 text-white hover:bg-blue-600 mt-5">Connexion</button>
            </form>
        </div>
        
        END;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS);

            try {
                $user = AuthenticationIdentityService::authenticate($email, $password);

                $db = ConnectionFactory::getConnection();

                $query = $db->prepare('SELECT * FROM user WHERE email = ?');
                $query->execute([$email]);
                $result = $query->fetch();

                if ((int)$result['active'] === 0) {
                    $html = "<p class='text-gray-900 dark:text-white'>Compte non activé</p>";
                } else {
                    $html .= "<p class='text-gray-900 dark:text-white'>Connexion réussi</p>";
                    $html .= "<p class='text-gray-900 dark:text-white'>Bienvenue $email</p>";
                    $_SESSION['loggedUser'] = serialize($user);

                    header('Location: index.php');
                    exit();
                }
            } catch (AuthenticationException $e) {
                $html .= "<p class='text-gray-900 dark:text-white'>Compte inexistant</p>";
            } catch (BadPasswordException $e) {
                $html .= "<p class='text-gray-900 dark:text-white'>Mot de passe incorrect</p>";
            }
        }

        return $html;
    }
}
