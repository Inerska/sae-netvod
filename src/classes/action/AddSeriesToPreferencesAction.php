<?php

namespace Application\action;

use Application\datalayer\repository\SeriesRepository;

class AddSeriesToPreferencesAction extends Action
{
    public function execute(): string
    {
        if ($this->httpMethod === 'GET') {
            echo "<p class='text-gray-900 dark:text-white'>test</p>";


            $seriesId = (int)$_GET['seriesId'];
            echo "<p class='text-gray-900 dark:text-white'>$seriesId</p>";
            $user = unserialize($_SESSION['loggedUser'], ['allowed_classes' => true]);
            $repository = new SeriesRepository();
            $repository->addSeriesToPreferences($seriesId, $user->id);

            return "<p class='text-gray-900 dark:text-white'>La série a bien été ajoutée à vos préférences</p>";
        }
        return "";
    }
}