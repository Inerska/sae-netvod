<?php

namespace Application\action;

use Application\datalayer\repository\SeriesRepository;

class AddSeriesToPreferencesAction extends Action
{
    public function execute(): string
    {
        if ($this->httpMethod === 'GET') {
            $seriesId = (int)$_GET['seriesId'];
            $user = unserialize($_SESSION['loggedUser'], ['allowed_classes' => true]);
            $repository = new SeriesRepository();
            $repository->addSeriesToPreferences($seriesId, $user->id);

            if (isset($_GET['url'])) {
                header('Location: ' . $_GET['url']);
            } else {
                header('Location: index.php');
            }
        }
        return '';

    }
}