<?php

declare(strict_types=1);

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\exception\datalayer\DatabaseConnectionException;
use Application\exception\identity\AuthenticationException;
use Application\exception\identity\BadPasswordException;
use Application\identity\authentication\service\AuthenticationIdentityService;
use Application\service\AntiCsrfProtectionTokenGeneratorService;
use Exception;

class SigninAction extends Action
{

    /**
     * @throws DatabaseConnectionException
     * @throws Exception
     */
    public function execute(): string
    {

        if (isset($_SESSION['loggedUser'])) {
            header('Location: index.php');
        }

        if ($this->httpMethod === 'GET') {
            $service = new AntiCsrfProtectionTokenGeneratorService();
            $token = $service->protect();

            $html = <<<END
            <form method="post" class="flex justify-center items-center flex-col h-screen pb-72">
                <input type="hidden" name="token" value="$token">
                <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Se connecter</h1>
                <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                    <div class="flex flex-col gap-3 w-full">
                            <input class="flex-1 w-full p-2 focus:outline-none bg-transparent border-b-2 mb-5 dark:text-gray-100 placeholder-gray-200 focus:border-red-600 transition duration-300" type="email" name="email" oncopy="return false" onpaste="return false" placeholder="Email">
                            <input class="flex-1 w-full p-2 focus:outline-none bg-transparent border-b-2 dark:text-gray-100 placeholder-gray-200 focus:border-red-600 transition duration-300" type="password" name="password" oncopy="return false" onpaste="return false" placeholder="Mot de passe">
                            <a href="?action=renew" class="text-gray-900 dark:text-white font-sm text-xs">Mot de passe oublié ?</a>
                    </div>
                </div>
                <button type="submit" class="transition duration-300 bg-red-700 p-3 font-semibold text-white dark:text-gray-800 hover:bg-red-600 mt-5 uppercase">Se connecter</button>
                <span class="dark:text-white pt-5">Pas de compte encore ? <a href="?action=sign-up" class="text-red-700 font-semibold uppercase">Créer un compte</a></span>
            </form>
            
            END;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS);

            $service = new AntiCsrfProtectionTokenGeneratorService();
            $token = $_POST['token'];

            if (!$service->verify($token, 60*5)) {
                $html = <<<END
                <div class="flex justify-center items-center flex-col h-screen">
                    <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Se connecter</h1>
                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                        <div class="flex flex-col gap-3 w-full">
                            <span class="text-red-700 font-semibold">Une erreur est survenue, veuillez réessayer. Cela a peut-être été occasioné dû à un délai dépassé.</span>
                        </div>
                    </div>
                    <a href="?action=sign-in" class="transition duration-300 bg-red-700 p-3 font-semibold text-white dark:text-gray-800 hover:bg-red-600 mt-5 uppercase">Retour</a>
                </div>
                END;
            } else {
                try {
                    $user = AuthenticationIdentityService::authenticate($email, $password);

                    $db = ConnectionFactory::getConnection();

                    $query = $db->prepare('SELECT * FROM user WHERE email = ?');
                    $query->execute([$email]);
                    $result = $query->fetch();

                    if ((int)$result['active'] === 0) {
                        $html = <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Compte non activé</h1>
                                    </div>
                                </div>
                                END;
                    } else {
                        $html = <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Bienvenue $email</h1>  
                                END;

                        if($result['role'] == '100'){
                            $html .= <<<END
                                    <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Vous êtes admin</h1> 
                            END;
                            $_SESSION['loggedAdmin'] = true;
                        } else {
                            $_SESSION['loggedAdmin'] = false;
                        }
                        $_SESSION['loggedUser'] = serialize($user);
                        $html .= <<<END
                                    </div>
                                </div>
                                END;
                    }
                } catch (AuthenticationException $e) {
                    $html = <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Compte inexistant</h1>
                                    </div>
                                </div>
                                END;
                } catch (BadPasswordException $e) {
                    $html = <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Mot de passe incorrect</h1>
                                    </div>
                                </div>
                                END;
                }
            }
        }

        return $html;
    }
}
