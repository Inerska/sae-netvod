<?php

namespace Application\action;

use Application\datalayer\repository\SeriesRepository;

class AddSeriesToPreferencesAction extends Action
{
    public function execute(): string
    {
        if(!isset($_SESSION['loggedUser'])) {
            header('Location: index.php');
            exit();
        }

        if ($this->httpMethod === 'GET') {
            $seriesId = (int)$_GET['seriesId'];
            if ($seriesId > 0) {
                $user = unserialize($_SESSION['loggedUser'], ['allowed_classes' => true]);
                $repository = new SeriesRepository();
                $repository->addSeriesToPreferences($seriesId, $user->id);

                if (isset($_GET['url'])) {
                    $url = str_replace('search-ajax', 'search', $_GET['url']);
                    header('Location: ' . $url);
                    exit();
                }

            }

            header('Location: index.php');

        }
        return '';

    }
}