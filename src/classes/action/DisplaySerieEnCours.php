<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\render\SeriesCardRenderer;
use Application\video\Serie;

class DisplaySerieEnCours extends Action{

    public function execute(): string{

        $html = '';

        if (isset($_SESSION['loggedUser'])) {
            // recup la liste des serie en cours
            $user = unserialize($_SESSION['loggedUser']);
            $db = ConnectionFactory::getConnection();
            $stmt = $db->prepare("select idSerie, numEpisode from user_serie_en_cours where idUser = ?");
            $stmt->execute([$user->__get('id')]);

            $s = "";

            while($row = $stmt->fetch()){
                $serie = new Serie($row['idSerie']+0);
                $renderer = new SeriesCardRenderer($serie->image, $serie->titre, $serie->id, $serie->annee);
                $s .= $renderer->render();
            }

            if($s === ""){
                $html = "<h1 class='text-red-600 text-2xl font-bold' >Vous n'avez pas de serie en cours</h1>";
            }else{
                $html = <<<END
                    <h1 class='text-red-600 text-2xl font-bold' >Vos series en cours</h1>
                    <div class='flex flex-wrap'>
                    $s
                    </div>
                END;
            }

            // l'affiche
        }

        return $html;
    }
}