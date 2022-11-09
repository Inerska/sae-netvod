<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\render\SerieRenderer;
use Application\render\SeriesCardRenderer;
use Application\video\Serie;

class DisplayViewedAction extends Action {

    public function execute(): string
    {
        if (isset($_SESSION['loggedUser'])) {
            // recup la liste des visonnée
            $user = unserialize($_SESSION['loggedUser']);
            $db = ConnectionFactory::getConnection();
            $stmt = $db->prepare("select idSerie from user_serie_vu where idUser = ?");
            $stmt->execute([$user->__get('id')]);

            $s = "";
            while($row = $stmt->fetch()){
                $serie = new Serie($row['idSerie']+0);
                $renderer = new SeriesCardRenderer($serie->image, $serie->titre, $serie->id);
                $s .= $renderer->render();
            }

            // l'affiche

            if($s === ""){
                $html = "<h1 class='text-red-600 text-2xl font-bold'>Vous n'avez pas de serie déjà vu pour le moment</h1>";
            }else{
                $html = "<h1 class='text-red-600 text-2xl font-bold'>liste de vos series vu : </h1>" . $s;
            }
        }else{
            $html = "<p>Aucun utilisateur connecté</p>";
        }

        return $html;
    }
}