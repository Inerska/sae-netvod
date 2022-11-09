<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\exception\datalayer\DatabaseConnectionException;
use Application\render\SeriesCardRenderer;

class ViewCatalogueAction extends Action
{

    /**
     * @throws DatabaseConnectionException
     */
    public function execute(): string
    {
        $db = ConnectionFactory::getConnection();

        $query = $db->prepare("SELECT * FROM serie");
        $query->execute();

        $html = "";

        while ($result = $query->fetch()) {
            $seriesCard = new SeriesCardRenderer($result["img"], $result["titre"], $result["id"]);

            $html .= $seriesCard->render();
        }

        if ($html === "") {
            $html = "<p>Aucune série n'est disponible</p>";
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