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
            $html .= '<p>Vous êtes déjà connecté</p>';
        } else {
            if (!isset($_GET['token']) && !isset($_POST['token'])) {
                $service = new AntiCsrfProtectionTokenGeneratorService();
                if ($this->httpMethod === 'GET') {
                    $token = $service->protect();

                    $html .= <<<END
                    <form method="POST">
                        <input type="hidden" name="token" value="$token">
                        <input type="email" name="email" placeholder="Adresse mail" required>
                        <input type="submit" value="Renouveler">
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
                        <p>Un email de renouvellement a été envoyé à l'adresse $email</p>
                        <a href="index.php?action=renew&token={$token}">Changer le mot de passe</a>
                        END;
                        } else {
                            $html .= "<p>Cette adresse mail n'existe pas</p>";
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
                            <form method="POST">
                                <input type="hidden" name="token" value="$token">
                                <input type="hidden" name="tokenCsrf" value="$tokenCsrf">
                                <input type="password" name="password" placeholder="Nouveau mot de passe" required>
                                <input type="password" name="passwordConfirm" placeholder="Confirmation du mot de passe" required>
                                <input type="submit" value="Changer le mot de passe">
                            </form>
                            END;
                        } else {
                            $token = AuthenticationIdentityService::generateRenewToken($result['email']);
                            $html = '<p>Votre lien a expiré, un nouveau lien a été généré</p>';
                            $html .= "<p><a href='index.php?action=renew&token={$token}'>Changer le mot de passe</a></p>";
                        }
                    } else {
                        $html = '<p>Ce token ne correspond a aucun compte</p>';
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
                                    $query = $db->prepare('UPDATE user SET renewToken = NULL, renewExpiration = NULL, passwrd = :password WHERE id = :id');
                                    $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
                                    $query->execute([':id' => $result['id'], ':password' => $hash]);
                                    $html = '<p>Votre mot de passe a bien été changé</p>';
                                    $html .= "<a href='index.php?action=sign-in'>Se connecter</a>";
                                } else {
                                    $html = "<p>Votre mot de passe n'est pas assez fort</p>";
                                    $html .= "<a href='index.php?action=renew&token={$token}'>Changer le mot de passe</a>";
                                }
                            } else {
                                $html = '<p>Les mots de passe ne correspondent pas</p>';
                                $html .= "<a href='index.php?action=renew&token={$token}'>Changer le mot de passe</a>";
                            }
                        } else {
                            $html = '<p>Ce token ne correspond a aucun compte</p>';
                            $html .= "<a href='index.php?action=renew&token={$token}'>Changer le mot de passe</a>";
                        }
                    } else {
                        $html = '<p>Une erreur est survenue</p>';
                    }
                }
            }
        }

        return $html;
    }
}
