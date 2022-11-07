<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\exception\datalayer\DatabaseConnectionException;

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
            $name = htmlentities($result['titre'], ENT_HTML5, 'UTF-8');
            $html .= "<a href='index.php?action=viewSerie&id={$result['id']}'><img src='{$result['img']}' alt=\"Image representant la serie '{$name}'\"><br>{$name}</a><br><br>";
        }

        if ($html == "") {
            $html = "<p>Aucune série n'est disponible</p>";
        } else {
            $html = <<<END
            <div class="catalogue">
                {$html}
            </div>
            END;
        }

        return $html;
    }
}