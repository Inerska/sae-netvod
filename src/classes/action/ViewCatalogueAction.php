<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\exception\datalayer\DatabaseConnectionException;
use Application\render\SeriesCardRenderer;

class ViewCatalogueAction extends Action
{

    private array $listeTri = [
        'id' => 'Aucun tri',
        'titre' => 'Titre',
        'annee' => 'Année de sortie',
        'date_ajout' => "Date d'ajout",
        'note_moyenne' => 'Note Moyenne'
    ];

    /**
     * @throws DatabaseConnectionException
     */
    public function execute(): string
    {
        $db = ConnectionFactory::getConnection();

        $searchQuery = "SELECT * FROM serie";

        $tri = $_GET['tri'] ?? 'id';


        $html = <<<END
        <form method="get" action="index.php">
            <input type="hidden" name="action" value="viewCatalogue">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300" for="genrePrefere">Trier le catalogue</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="tri" id="tri"">
        END;

        if (array_key_exists($tri, $this->listeTri)) {
            $searchQuery .= " ORDER BY $tri";
            $html .= "<option value=\"{$tri}\">{$this->listeTri[$tri]}</option>";
            unset($this->listeTri[$tri]);
        } else {
            $html .= "<option value=\"id\">{$this->listeTri['id']}</option>";
            unset($this->listeTri['id']);
        }

        foreach ($this->listeTri as $key => $value) {
            $html .= "<option value=\"{$key}\">{$value}</option>";
        }

        $html .= <<<END
            </select>
            <button class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">Trier</button>
            END;

        $query = $db->prepare($searchQuery);
        $query->execute();

        while ($result = $query->fetch()) {
            $seriesCard = new SeriesCardRenderer($result["img"], $result["titre"], $result["id"]);

            $html .= $seriesCard->render();
        }

        if ($html === "") {
            $html = "<p>Aucune série ne correspond à la recherche</p>";
        } else {
            $html = <<<END
            <div class="flex flex-wrap flex-row gap-10">
                {$html}
            </div>
            END;
        }

        return $html;
    }
}