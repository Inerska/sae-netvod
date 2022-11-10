<?php

namespace Application\action;

use Application\exception\datalayer\DatabaseConnectionException;
use Application\exception\identity\AuthenticationException;
use Application\identity\authentication\service\AuthenticationIdentityService;
use Application\service\AntiCsrfProtectionTokenGeneratorService;

class SignupAction extends Action
{
    final public function execute(): string
    {

        if (isset($_SESSION['loggedUser'])) {
            header('Location: index.php');
        }

        if ($this->httpMethod === 'GET') {
            $service = new AntiCsrfProtectionTokenGeneratorService();
            $token = $service->protect();

            return <<<END
            <form method="post" class="flex justify-center items-center flex-col h-screen pb-72">
                <input type="hidden" name="token" value="$token">
                <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">S'enregistrer</h1>
                <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                    <div class="flex flex-col gap-3 w-full">
                            <input class="flex-1 w-full p-2 focus:outline-none bg-transparent border-b-2 dark:text-gray-100 placeholder-gray-200 focus:border-red-600 transition duration-300" type="email" name="email" oncopy="return false" onpaste="return false" placeholder="Email">
                            <input class="flex-1 w-full p-2 focus:outline-none bg-transparent border-b-2 dark:text-gray-100 placeholder-gray-200 focus:border-red-600 transition duration-300" type="password" name="password" oncopy="return false" onpaste="return false" placeholder="Mot de passe">
                            <input class="flex-1 w-full p-2 focus:outline-none bg-transparent border-b-2 dark:text-gray-100 placeholder-gray-200 focus:border-red-600 transition duration-300" type="password" name="confirm" oncopy="return false" onpaste="return false" placeholder="Confirmer votre mot de passe">
                    </div>
                </div>
                <button type="submit" class="transition duration-300 bg-red-700 p-3 text-white font-semibold hover:bg-red-600 mt-5 uppercase">S'enregistrer</button>
                <span class="dark:text-white pt-5">Vous avez déjà un compte ? <a href="?action=sign-in" class="text-red-700 font-semibold uppercase">Connectez-vous</a></span>
            </form>
            END;
        }

        if ($this->httpMethod === 'POST') {
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'];
            $confirm = $_POST['confirm'];

            $service = new AntiCsrfProtectionTokenGeneratorService();
            $valid = $service->verify($_POST['token'], 60 * 5);

            if ($valid) {
                try {
                    $token = AuthenticationIdentityService::register($email, $password, $confirm);
                } catch (DatabaseConnectionException|AuthenticationException $e) {
                    return $e->getMessage();
                }

                return <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Compte enregister avec succès, veuillez activer votre compte</h1>
                                        <a href='index.php?action=activation&token={$token}' class="text-gray-900 dark:text-white font-sm text-lg">Activer mon compte</a>
                                    </div>
                                </div>
                                END;
            } else {
                return <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Une erreur est survenue, veuillez réessayer</h1>
                                    </div>
                                </div>
                                END;
            }
        }

        return '';
    }
}
