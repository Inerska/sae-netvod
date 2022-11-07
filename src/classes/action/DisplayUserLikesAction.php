<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;

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
            $query = "select likeou jsp dep de la bd";
            $stmt = $bd->prepare($query);
            $stmt->execute([$user->get('id')]);
            // affiche les likes
            $html = "<p>liste de vos likes : </p>";
            while ($row = $stmt->fetch()){
                $html .= "<p>{$row['jsp']}</p>";
            }
        }

        return $html;

    }
}