<?php

namespace Application\action;

if (!isset($_SESSION['loggedUser'])) {
    header('Location: index.php?action=sign-in');
    exit();
}

use Application\datalayer\factory\ConnectionFactory;
use Application\datalayer\repository\ProfileRepository;
use Application\datalayer\util\Gender;
use Application\service\AntiCsrfProtectionTokenGeneratorService;

class ProfileAction extends Action
{
    public function execute(): string
    {

        $html = "";

        if ($this->httpMethod === "GET") {
            $loggedUser = unserialize($_SESSION["loggedUser"], ["allowed_classes" => true]);
            $repository = new ProfileRepository();

            $profile = $repository->getProfileByUserId($loggedUser->id);

            if ($profile === null) {
                return "Profile not found";
            }

            $service = new AntiCsrfProtectionTokenGeneratorService();
            $token = $service->protect();

            $nom = htmlentities($profile->getNom());
            $prenom = htmlentities($profile->getPrenom());

            $html = <<<END
<h1 class="text-3xl font-bold mb-8 dark:text-white text-gray-900" xmlns="http://www.w3.org/1999/html">Modifier le profil</h1>
<form method="POST">
<input type="hidden" name="token" value="$token">
<div class="grid gap-6 mb-6 md:grid-cols-2">
<div>
<label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300" for="nom">Nom</label>
    <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="nom" id="nom" value="{$nom}">
</div>
<div>
    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300" for="prenom">Prenom</label>
    <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="prenom" id="prenom" value="{$prenom}"> 
</div>
<div>
<label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300" for="age">Age</label>
    <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="number" name="age" id="age" value="{$profile->getAge()}">
</div>   
<div>
<label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300" for="sexe">Sexe</label>
    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="sexe" id="sexe">
    <option value="{$profile->getGender()}">{$profile->getGender()}</option>
END;

            if ($profile->getGender() != "Masculin") {
                $html .= "<option value='Masculin'>Masculin</option>";
            }
            if ($profile->getGender() != "Feminin") {
                $html .= "<option value='Feminin'>Feminin</option>";
            }
            if ($profile->getGender() != "Autre") {
                $html .= "<option value='Autre'>Autre</option>";
            }
            $html .= <<<END
    </select>
</div>
<div>
   <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300" for="genrePrefere">Genre prefere</label>
    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="genrePrefere" id="genrePrefere"">
    <option value="{$profile->getGenrePrefere()}">{$profile->getGenrePrefere()}</option>
END;

            $db = ConnectionFactory::getConnection();
            $stmt = $db->prepare("SELECT libelle FROM genre WHERE libelle != :genrePrefere");
            $stmt->execute(['genrePrefere' => $profile->getGenrePrefere()]);

            while ($row = $stmt->fetch()) {
                $html .= "<option value='{$row["libelle"]}'>{$row["libelle"]}</option>";
            }

            $html .= <<<END
    </select>
</div>
    <input class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 hover:cursor-pointer" type="submit" value="Update">
</div>
</form>
END;

        } elseif ($this->httpMethod === "POST") {

            $token = $_POST["token"];
            $service = new AntiCsrfProtectionTokenGeneratorService();
            $valid = $service->verify($token, 60 * 5);

            if (!$valid) {
                return "Invalid token";
            }


            $loggedUser = unserialize($_SESSION["loggedUser"], ["allowed_classes" => true]);
            $repository = new ProfileRepository();

            $profile = $repository->getProfileByUserId($loggedUser->id);

            if ($profile === null) {
                return "Profile not found";
            }

            $profile->setNom($_POST["nom"]);
            $profile->setPrenom($_POST["prenom"]);
            $profile->setAge($_POST["age"]);
            $profile->setGender($_POST["sexe"]);
            $profile->setGenrePrefere($_POST["genrePrefere"]);

            $repository->updateProfile($profile);

            header('Location: index.php?action=profile');
            echo "dgfdgd";
            exit();
        }

        return $html;
    }
}