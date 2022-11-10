<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\identity\authentication\service\AuthenticationIdentityService;
use Application\identity\authentication\service\PasswordStrengthCheckerService;
use Application\service\AntiCsrfProtectionTokenGeneratorService;

class RenewAction extends Action
{

    public function execute(): string
    {
        $html = '';

        if (isset($_SESSION['loggedUser'])) {
            $html .= <<<END
                        <div class="flex justify-center items-center flex-col h-screen pb-72">
                            <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Vous êtes déjà connecté</h1>
                            </div>
                        </div>
                        END;
        } else {

            if (!isset($_GET['token']) && !isset($_POST['token'])) {
                $service = new AntiCsrfProtectionTokenGeneratorService();
                if ($this->httpMethod === 'GET') {
                    $token = $service->protect();

                    $html .= <<<END
                    <form method="POST" class="flex justify-center items-center flex-col h-screen pb-72">
                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Renouveler le mot de passe</h1>
                        <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                            <div class="flex flex-col gap-3 w-full">
                                <input type="hidden" name="token" value="$token">
                                <input class="flex-1 w-full p-2 focus:outline-none bg-transparent border-b-2 mb-5 dark:text-gray-100 placeholder-gray-200 focus:border-red-600 transition duration-300" type="email" name="email" placeholder="Adresse mail" required>
                            </div>
                        </div>
                        <button type="submit" value="Renouveler" class="transition duration-300 bg-red-700 p-3 font-semibold text-white dark:text-gray-800 hover:bg-red-600 mt-5 uppercase">Renouveler</button>

                    </form>
                    END;
                } elseif ($this->httpMethod === 'POST') {
                    $email = $_POST['email'];

                    $valid = $service->verify($_POST['token'], 60 * 5);

                    if ($valid) {
                        $db = ConnectionFactory::getConnection();
                        $query = $db->prepare('SELECT id FROM user WHERE email = :email');
                        $query->execute([':email' => $email]);

                        if ($query->fetch()) {
                            $token = AuthenticationIdentityService::generateRenewToken($email);
                            $html .= <<<END
                            <div class="flex justify-center items-center flex-col h-screen pb-72">
                                <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                    <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Un email de renouvellement a été envoyé à l'adresse $email</h1>
                                    <a href="index.php?action=renew&token={$token}" class="text-gray-900 dark:text-white font-sm text-lg">Changer le mot de passe</a>
                                </div>
                            </div>
                            END;
                        } else {
                            $html .= <<<END
                            <div class="flex justify-center items-center flex-col h-screen pb-72">
                                <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                    <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Cette adresse mail n'existe pas</h1>
                                </div>
                            </div>
                            END;
                        }
                    } else {
                        $html .= '<p>Une erreur est survenue</p>';
                    }
                }

            } else {
                if ($this->httpMethod === 'GET') {
                    $token = $_GET['token'];
                    $db = ConnectionFactory::getConnection();
                    $query = $db->prepare('SELECT id, email, renewExpiration FROM user WHERE renewToken = :token');
                    $query->execute([':token' => $token]);

                    $service = new AntiCsrfProtectionTokenGeneratorService();
                    $tokenCsrf = $service->protect();

                    if ($result = $query->fetch()) {
                        if ($result['renewExpiration'] > time()) {
                            $html .= <<<END
                            <form method="POST" class="flex justify-center items-center flex-col h-screen pb-72">
                                <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Se connecter</h1>
                                <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                    <div class="flex flex-col gap-3 w-full">
                                        <input type="hidden" name="tokenCsrf" value="$tokenCsrf">
                                        <input type="hidden" name="token" value="$token">
                                        <input class="flex-1 w-full p-2 focus:outline-none bg-transparent border-b-2 mb-5 dark:text-gray-100 placeholder-gray-200 focus:border-red-600 transition duration-300" type="password" name="password" placeholder="Nouveau mot de passe" required>
                                        <input class="flex-1 w-full p-2 focus:outline-none bg-transparent border-b-2 dark:text-gray-100 placeholder-gray-200 focus:border-red-600 transition duration-300" type="password" name="passwordConfirm" placeholder="Confirmation du mot de passe" required>
                                    </div>
                                </div>
                                <button type="submit" class="transition duration-300 bg-red-700 p-3 font-semibold text-white dark:text-gray-800 hover:bg-red-600 mt-5 uppercase" value="Changer le mot de passe">Changer le mot de passe</button>
                            </form>
                            END;
                        } else {
                            $token = AuthenticationIdentityService::generateRenewToken($result['email']);
                            $html .= <<<END
                            <div class="flex justify-center items-center flex-col h-screen pb-72">
                                <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                    <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Votre lien a expiré, un nouveau lien a été généré</h1>
                                    <a href="index.php?action=renew&token={$token}" class="text-gray-900 dark:text-white font-sm text-lg">Changer le mot de passe</a>
                                </div>
                            </div>
                            END;
                        }

                    } else {
                        $html .= <<<END
                        <div class="flex justify-center items-center flex-col h-screen pb-72">
                                <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                    <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Ce token ne correspond a aucun compte</h1>
                                </div>
                            </div>
                        END;
                    }
                } elseif ($this->httpMethod === 'POST') {
                    $token = $_POST['token'];
                    $tokenCsrfForm = $_POST['tokenCsrf'];

                    $service = new AntiCsrfProtectionTokenGeneratorService();
                    $valid = $service->verify($tokenCsrfForm, 60 * 5);

                    if ($valid) {
                        $db = ConnectionFactory::getConnection();
                        $query = $db->prepare('SELECT id, email FROM user WHERE renewToken = :token');
                        $query->execute([':token' => $token]);

                        if ($result = $query->fetch()) {
                            $password = $_POST['password'];
                            $passwordConfirm = $_POST['passwordConfirm'];

                            if ($password === $passwordConfirm) {

                                if (PasswordStrengthCheckerService::check($password)) {
                                    $query = $db->prepare("UPDATE user SET renewToken = NULL, renewExpiration = NULL, passwrd = :password WHERE id = :id");
                                    $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
                                    $query->execute([':id' => $result['id'], ':password' => $hash]);
                                    $html .= <<<END
                                    <div class="flex justify-center items-center flex-col h-screen pb-72">
                                        <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                            <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Votre mot de passe a bien été changé</h1>
                                            <a href="index.php?action=sign-in" class="text-gray-900 dark:text-white font-sm text-lg">Se connecter</a>
                                        </div>
                                    </div>
                                    END;
                                } else {
                                    $html .= <<<END
                                    <div class="flex justify-center items-center flex-col h-screen pb-72">
                                        <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                            <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Votre mot de passe n'est pas assez fort</h1>
                                            <a href="index.php?action=renew&token={$token}" class="text-gray-900 dark:text-white font-sm text-lg">Changer le mot de passe</a>
                                        </div>
                                    </div>
                                    END;
                                }

                            } else {
                                $html .= <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Les mots de passe ne correspondent pas</h1>
                                        <a href="index.php?action=renew&token={$token}" class="text-gray-900 dark:text-white font-sm text-lg">Changer le mot de passe</a>
                                    </div>
                                </div>
                                END;
                            }

                        } else {
                            $html .= <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Ce token ne correspond a aucun compte</h1>
                                    </div>
                                </div>
                                END;
                        }
                    }
                }
            }
        }
        return $html;
    }
}