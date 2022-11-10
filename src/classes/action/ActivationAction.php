<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\identity\authentication\service\AuthenticationIdentityService;
use Application\identity\model\User;

class ActivationAction extends Action
{

    public function execute(): string
    {
        $html = "";
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            $db = ConnectionFactory::getConnection();
            $query = $db->prepare("SELECT id, email, activationExpiration FROM user WHERE activationToken = :token");
            $query->execute([':token' => $token]);
            if($result = $query->fetch()) {
                if ($result['activationExpiration'] > time()) {
                    $query = $db->prepare("UPDATE user SET activationToken = NULL, activationExpiration = NULL, active = 1 WHERE id = :id");
                    $query->execute([':id' => $result['id']]);
                    $_SESSION['loggedUser'] = serialize(new User($result['id'], $result['email']));

                    $html = <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Votre compte a bien été activé</h1>
                                    </div>
                                </div>
                                END;
                } else {
                    $token = AuthenticationIdentityService::generateActivationToken($result['id']);
                    $html = <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Votre lien d'activation a expiré, un nouveau lien a été généré</h1>
                                        <a href='index.php?action=activation&token={$token}' class="text-gray-900 dark:text-white font-sm text-lg">Activer mon compte</a>
                                    </div>
                                </div>
                                END;
                }

            } else {
                $html = <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Ce token ne correspond a aucun compte</h1>
                                    </div>
                                </div>
                                END;
            }

        } else {
            $html = <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Erreur lors de l'activation</h1>
                                    </div>
                                </div>
                                END;
        }
        return $html;
    }
}