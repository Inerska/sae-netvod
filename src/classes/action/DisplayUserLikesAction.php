<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\render\SerieRenderer;
use Application\video\Serie;

class DisplayUserLikesAction extends Action{

    public function execute(): string
    {
        if(!isset($_SESSION['loggedUser'])){
            $html = "<p>Aucun utilisateur connecté</p>";
        }else{
            // get l'user en session
            $user = unserialize($_SESSION['loggedUser']);
            // get ses likes dans la bd grace a son id
            $bd = ConnectionFactory::getConnection();
            $query = "select idSerie from user_serie_Pref where idUser = ?";
            $stmt = $bd->prepare($query);
            $stmt->execute([$user->__get('id')]);
            // affiche les likes
            $html = "<p>liste de vos likes : </p>";
            while ($row = $stmt->fetch()){
                // on affiche toutes les series qu'il a like

                // cree une serie a partir de l'id
                $serie = new Serie($row['idSerie']+0);
                // cree un renderer
                $renderer = new SerieRenderer($serie);
                // l'affiche
                $html .= $renderer->render();
            }
        }

        return $html;
    }
}