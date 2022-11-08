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
            $numEpisode = $_GET['episodeId'];

            $episode = new Episode($serieId+0, $numEpisode+0);
            $renderer = new EpisodeRenderer($episode);

            $html = $renderer->longRender();

            if (isset($_SESSION['loggedUser'])){
                $user = unserialize($_SESSION['loggedUser']);
                $db = ConnectionFactory::getConnection();
                $stmt = $db->prepare("select * from user_serie_en_cours where idUser = ? and idSerie = ?");
                $stmt->execute([$user->__get('id')+0, $serieId+0]);
                $data = $stmt->fetch();
                // si la serie est deja en cours, on regarde l'episode et si l'episode courant et sup a l'episode dans la base on modifie
                if ($data){
                    $numEpisodeDB = $data['numEpisode'];
                    if ($numEpisode > $numEpisodeDB){
                        $stmt = $db->prepare("update user_serie_en_cours set numEpisode = ? where idUser = ? and idSerie = ?");
                        $stmt->execute([$numEpisode, $user->__get('id')+0, $serieId+0]);
                    }
                }else{
                // sinon, on l'ajoute avec l'episode que l'on vient de regarder
                    $stmt = $db->prepare("insert into user_serie_en_cours (idUser, idSerie, numEpisode) values (?, ?, ?)");
                    $stmt->execute([$user->__get('id')+0, $serieId+0, $numEpisode+0]);
                    $html .= "<p>Ajouter à la liste de visionnage en cours</p>";
                }





            }


        }
        $html .= "<a href='index.php'>Retour page principale</a>";
        return $html;
    }
}