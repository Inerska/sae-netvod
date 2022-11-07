<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;

class DisplaySerieEpisodeAction extends Action{

    public function execute(): string{
       // get l'id de la seerie et l'id de l'episode
        $_GET['episodeId'];
        if(!isset($_GET['episodeId']) && !isset($_GET['serieId'])){
            $html = "<p>Erreur lors de l'affichage</p>";
        }else{
            $serieId = $_GET['serieId'];
            $episodeId = $_GET['episodeId'];

            $db = ConnectionFactory::getConnection();
            $query = "select titre, resume, duree from episode where id = ? and serie_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$episodeId, $serieId]);

            $row = $stmt->fetch();

            $html = "<p>Titre de l'epiosde : {$row['titre']}</p>";
            $html .= "<p>Resumé de l'épiosde : {$row['resume']}</p>";
            $html .= "<p>Durée de l'épiosde : {$row['duree']}</p>";
        }
        $html .= "<a href='index.php'>Retour page principale</a>";
        return $html;
    }
}