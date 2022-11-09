<?php

namespace Application\action;

use Application\datalayer\repository\SeriesRepository;
use Application\render\SeriesCardRenderer;

class SearchSeriesAction extends Action
{
    public function execute(): string
    {
        return match ($this->httpMethod) {
            'POST' => $this->post(),
            default => $this->get(),
        };
    }

    private function post(): string
    {
        $search = filter_var($_POST['search'], FILTER_SANITIZE_SPECIAL_CHARS);
        $repository = new SeriesRepository();
        $series = $repository->getSeriesWith($search);

        $html = <<<END
        <form method="post" class="w-100 flex justify-center items-center">
            <input class="border-b-4 bg-gray-100 dark:bg-gray-900 dark:text-gray-100 h-10" type="text" name="search" placeholder="Rechercher...">
        </form>
        <script src="js/ajax.js"></script>
        END;

        foreach ($series as $serie) {
            $renderer = new SeriesCardRenderer($serie['img'], $serie['titre'], $serie['id']);
            $html .= $renderer->render();
        }

        return $html;
    }

    private function get(): string
    {
        return <<<END
        <form method="post" class="w-100 flex justify-center items-center bg-gray-800">
            <input class="border-b-4 bg-gray-100 dark:bg-gray-900 dark:text-gray-100 h-10" type="text" name="search" placeholder="Rechercher...">
        </form>
        <script src="js/ajax.js"></script>
        END;
    }
}