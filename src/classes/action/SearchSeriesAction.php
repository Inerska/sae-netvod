<?php

namespace Application\action;

use Application\datalayer\repository\SeriesRepository;
use Application\render\SeriesCardRenderer;

class SearchSeriesAction extends Action
{

    private string $htmlBase = <<<END
        <form method="post" class="w-screen flex justify-center items-center bg-white dark:bg-gray-800 h-20 mb-10 flex-col border-red-700 m-0">
            <input class="flex-1 w-1/2 text-5xl border-b-2 border-red-600 my-10 bg-transparent dark:bg-gray-800 dark:text-gray-100 focus:outline-none" type="text" name="search" placeholder="Rechercher...">
        </form>
        <div class="flex flex-wrap justify-center" id="articles"></div>
        <script src="js/ajax.js"></script>
    END;


    public function execute(): string
    {
        if (!isset($_SESSION['loggedUser'])) {
            header('Location: index.php');
            exit();
        }

        return match ($this->httpMethod) {
            'POST' => $this->post(),
            default => $this->get(),
        };
    }

    private function post(): string
    {
        $repository = new SeriesRepository();
        $series = $repository->getSeriesWith($_POST['search']);
        $html = "";

        foreach ($series as $serie) {
            $renderer = new SeriesCardRenderer($serie['img'], $serie['titre'], $serie['id'], $serie['annee']);
            $html .= $renderer->render();
        }

        return $html;
    }

    private function get(): string
    {
        return $this->htmlBase;
    }
}