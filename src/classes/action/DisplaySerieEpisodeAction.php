<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\render\EpisodeRenderer;
use Application\video\Episode;

class DisplaySerieEpisodeAction extends Action{

    public function execute(): string{
       // get l'id de la seerie et l'id de l'episode
        if(!isset($_GET['episodeId']) && !isset($_GET['serieId'])){
            $html = "<p>Erreur lors de l'affichage</p>";
        }else{
            $serieId = $_GET['serieId'];
            $episodeId = $_GET['episodeId'];

            $episode = new Episode($serieId+0, $episodeId+0);
            $renderer = new EpisodeRenderer($episode);

            $html = $renderer->longRender();

            if (isset($_SESSION['loggedUser'])){
                // ajoute cette serie a la liste de serie en cours
                $db = ConnectionFactory::getConnection();
                $user = unserialize($_SESSION['loggedUser']);
                $stmt = $db->prepare("select * from user_serie_en_cours where idUser = ? and idSerie = ?");
                $stmt->execute([$user->__get('id')+0, $serieId+0]);
                $data = $stmt->fetch();
                if (!$data){
                    $stmt = $db->prepare("insert into user_serie_en_cours (idUser, idSerie, numEpisode) values (?, ?, ?)");
                    $stmt->execute([$user->__get('id')+0, $serieId+0, $episodeId+0]);
                    $html .= "<p>Ajouter à la liste de visionnage en cours</p>";
                }


            }


        }
        $html .= "<a href='index.php'>Retour page principale</a>";
        return $html;
    }
}