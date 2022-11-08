<?php

namespace Application\action;

if (!isset($_SESSION['loggedUser'])) {
    header('Location: index.php?action=sign-in');
    exit();
}

use Application\datalayer\repository\ProfileRepository;
use Application\datalayer\util\Gender;
use Application\identity\model\User;

class ViewProfileAction extends Action
{
    public function execute(): string
    {
        $loggedUser = unserialize($_SESSION["loggedUser"], ["allowed_classes" => true]);
        $repository = new ProfileRepository();

        echo "va";

        $profile = $repository->getProfileByUserId($loggedUser->id);

        if ($profile === null) {
            return "Profile not found";
        }

        echo $profile->getAge() ?? -1;
        echo $profile->getGenrePrefere() ?? "null";
        echo $profile->getGender() ?? "null";
        echo $profile->getNom() ?? "null";
        echo $profile->getPrenom() ?? "null";

        return "";
    }
}