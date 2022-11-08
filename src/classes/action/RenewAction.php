<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\identity\authentication\service\AuthenticationIdentityService;
use Application\identity\model\User;

class RenewAction extends Action
{

    public function execute(): string
    {
        $html = "";

        if (isset($_SESSION['loggedUser'])) {
            $html .= "<p>Vous êtes déjà connecté</p>";
        } else {

            if (!isset($_GET['token']) or !isset($_POST['token'])) {
                if ($this->httpMethod === 'GET') {

                    $html .= <<<END
                    <form method="POST">
                        <input type="email" name="email" placeholder="Adresse mail" required>
                        <input type="submit" value="Renouveler">
                    </form>
                    END;

                } else if ($this->httpMethod === 'POST') {
                    $email = $_POST['email'];

                    $db = ConnectionFactory::getConnection();
                    $query = $db->prepare("SELECT id FROM user WHERE email = :email");
                    $query->execute([':email' => $email]);

                    if($query->fetch()) {
                        $token = AuthenticationIdentityService::generateRenewToken($email);
                        $html .= <<<END
                        <p>Un email de renouvellement a été envoyé à l'adresse $email</p>
                        <a href="index.php?action=renew&token={$token}">Changer le mot de passe</a>
                        END;
                        $html .= "<p>Cette adresse mail n'existe pas</p>";
                    }

                }

            } else {
                if ($this->httpMethod === 'GET') {
                    $token = $_GET['token'];
                    $db = ConnectionFactory::getConnection();
                    $query = $db->prepare("SELECT id, email, renewExpiration FROM user WHERE renewToken = :token");
                    $query->execute([':token' => $token]);
                    if($result = $query->fetch()) {
                        if ($result['renewExpiration'] > time()) {
                            $html .= <<<END
                            <form method="POST">
                                <input type="hidden" name="token" value="$token">
                                <input type="password" name="password" placeholder="Nouveau mot de passe" required>
                                <input type="password" name="passwordConfirm" placeholder="Confirmation du mot de passe" required>
                                <input type="submit" value="Changer le mot de passe">
                            </form>
                            END;

                        } else {
                            $token = AuthenticationIdentityService::generateRenewToken($result['email']);
                            $html = "<p>Votre lien a expiré, un nouveau lien a été généré</p>";
                            $html .= "<p><a href='index.php?action=renew&token={$token}'>Changer le mot de passe</a></p>";
                        }

                    } else {
                        $html = "<p>Ce token ne correspond a aucun compte</p>";
                    }
                } else if ($this->httpMethod === 'POST') {
                    $token = $_POST['token'];
                    $password = $_POST['password'];
                    $passwordConfirm = $_POST['passwordConfirm'];
                    if ($password === $passwordConfirm) {
                        $db = ConnectionFactory::getConnection();
                        $query = $db->prepare("SELECT id, email FROM user WHERE renewToken = :token");
                        $query->execute([':token' => $token]);
                        if($result = $query->fetch()) {
                            $query = $db->prepare("UPDATE user SET renewToken = NULL, renewExpiration = NULL, password = :password WHERE id = :id");
                            $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
                            $query->execute([':id' => $result['id'], ':password' => $hash]);
                            $html = "<p>Votre mot de passe a bien été changé</p>";
                            $html .= "<a href='index.php?action=sign-in'>Se connecter</a>";
                        } else {
                            $html = "<p>Ce token ne correspond a aucun compte</p>";
                        }
                    } else {
                        $html = "<p>Les mots de passe ne correspondent pas</p>";
                    }
                }
            }
        }

        return $html;
    }
}