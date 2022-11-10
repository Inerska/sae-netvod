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
        <form method="post" class="flex justify-center items-center flex-col h-screen pb-72">
            <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Se connecter</h1>
            <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                <div class="flex flex-col gap-3 w-full">
                        <input class="flex-1 w-full p-2 focus:outline-none bg-transparent border-b-2 mb-5 dark:text-gray-100 placeholder-gray-200 focus:border-red-600 transition duration-300" type="email" name="email" oncopy="return false" onpaste="return false" placeholder="Email">
                        <input class="flex-1 w-full p-2 focus:outline-none bg-transparent border-b-2 dark:text-gray-100 placeholder-gray-200 focus:border-red-600 transition duration-300" type="password" name="password" oncopy="return false" onpaste="return false" placeholder="Mot de passe">
                        <a href="?action=renew" class="text-gray-900 dark:text-white font-sm text-xs">Mot de passe oublié ?</a>
                </div>
            </div>
            <button type="submit" class="transition duration-300 bg-red-700 p-3 text-gray-800 font-semibold hover:bg-red-600 mt-5 uppercase">Se connecter</button>
            <span class="dark:text-white pt-5">Pas de compte encore ? <a href="?action=sign-up" class="text-red-700 font-semibold uppercase">Créer un compte</a></span>
        </form>
        
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
