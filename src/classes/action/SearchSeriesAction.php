<?php

namespace Application\action;

use Application\datalayer\repository\SeriesRepository;
use Application\render\SeriesCardRenderer;

class SearchSeriesAction extends Action
{

    private string $htmlBase = <<<END
        <form method="post" class="w-100 flex justify-center items-center bg-gray-100 dark:bg-gray-800 h-20 mb-10 flex-col border-red-700">
            <input class="flex-1 w-full border-b-4 border-red-600 bg-gray-100 dark:bg-gray-800 dark:text-gray-100 p-5" type="text" name="search" placeholder="Rechercher...">
        </form>
        <script src="js/ajax.js"></script>
    END;


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

        $html = $this->htmlBase;

        $html .= "<div class='flex flex-wrap flex-row gap-10'>";
        foreach ($series as $serie) {
            $renderer = new SeriesCardRenderer($serie['img'], $serie['titre'], $serie['id']);
            $html .= $renderer->render();
        }
        $html .= "</div>";

        return $html;
    }

    private function get(): string
    {
        return $this->htmlBase;
    }
}