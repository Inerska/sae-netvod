<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\exception\datalayer\DatabaseConnectionException;
use Application\render\SeriesCardRenderer;

class ViewCatalogueAction extends Action
{

    private array $listeTri = [
        'Pas de tri' => 'id',
        'Titre' => 'titre',
        'Année de sortie' => 'annee',
        "Date d'ajout" => 'date_ajout',
    ];

    /**
     * @throws DatabaseConnectionException
     */
    public function execute(): string
    {
        $db = ConnectionFactory::getConnection();

        $searchQuery = "SELECT * FROM serie";

        $tri = $_GET['tri'] ?? 'Pas de tri';

        /*if () {
            $searchQuery .= " ORDER BY $tri";
        }*/

        $query = $db->prepare($searchQuery);
        $query->execute();

        $html = "";

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