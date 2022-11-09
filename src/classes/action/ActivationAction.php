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

                    $html = "<p>Votre compte a bien été activé</p>";
                } else {
                    $token = AuthenticationIdentityService::generateActivationToken($result['id']);
                    $html = "<p>Votre lien d'activation a expiré, un nouveau lien a été généré</p>";
                    $html .= "<p><a href='index.php?action=activation&token={$token}'>Activer mon compte</a></p>";
                }

            } else {
                $html = "<p>Ce token ne correspond a aucun compte</p>";
            }

        } else {
            $html = "<p>Erreur lors de l'activation</p>";
        }
        return $html;
    }
}