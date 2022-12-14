<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\render\SerieRenderer;
use Application\render\SeriesCardRenderer;
use Application\video\Serie;

class DisplayUserLikesAction extends Action
{

    public function execute(): string
    {

        $html = '';

        if (isset($_SESSION['loggedUser'])) {
            // get l'user en session
            $user = unserialize($_SESSION['loggedUser']);
            // get ses likes dans la bd grace a son id
            $bd = ConnectionFactory::getConnection();
            $query = 'select idSerie from user_serie_pref where idUser = ?';
            $stmt = $bd->prepare($query);
            $stmt->execute([$user->__get('id')]);
            // affiche les likes

            $s = "";


            while ($row = $stmt->fetch()) {
                // on affiche toutes les series qu'il a like

                // cree une serie a partir de l'id
                $serie = new Serie($row['idSerie'] + 0);
                // cree un renderer
                // si on est sur la page principale
                $renderer = new SeriesCardRenderer($serie->image, $serie->titre, $serie->id, (int)$serie->annee);

                // l'affiche
                $s .= $renderer->render();
            }

            if($s === ""){
                $html = "<h1 class='text-red-600 text-2xl font-bold' >Vous n'avez pas de likes</h1>";
            }else{
                $html = <<<END
                    <h1 class='text-red-600 text-2xl font-bold' >Vos likes</h1>
                    <div class='flex flex-wrap'>
                    $s
                    </div>
                END;
            }


        }


        return $html;
    }
}
