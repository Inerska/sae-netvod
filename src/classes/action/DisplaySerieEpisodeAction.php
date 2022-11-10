<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\render\EpisodeNotationRenderer;
use Application\render\EpisodeRenderer;
use Application\video\Episode;
use Application\video\Serie;

class DisplaySerieEpisodeAction extends Action
{

    public function execute(): string
    {
        // get l'id de la seerie et l'id de l'episode
        if (!isset($_GET['numEp']) && !isset($_GET['serieId'])) {
            $html = "<p>Erreur lors de l'affichage</p>";
        } else {
            $serieId = $_GET['serieId'];
            $numEpisode = $_GET['numEp'];

            $episode = new Episode($serieId+0, $numEpisode+0);
            $renderer = new EpisodeNotationRenderer($episode);

            $html = $renderer->render();

            if (isset($_SESSION['loggedUser'])) {
                $user = unserialize($_SESSION['loggedUser']);
                $db = ConnectionFactory::getConnection();


                $stmt = $db->prepare("select * from user_serie_vu where idUser = ? and idSerie = ?");
                $stmt->execute([$user->__get('id') + 0, $serieId + 0]);
                $data = $stmt->fetch();

                // si la serie est deja vu, on ne fait rien
                if (!$data) {
                    // sinon
                    // si l'episode courant est le dernier
                    $serie = new Serie($serieId);
                    if ($serie->__get('nbEpisodes') == $numEpisode) {
                        // on ajoute la serie aux series deja vu
                        $stmt = $db->prepare("insert into user_serie_vu values (?, ?)");
                        $stmt->execute([$user->__get('id') + 0, $serieId + 0]);
                        // on le supprime des series en cours
                        $stmt = $db->prepare("delete from user_serie_en_cours where idUser = ? and idSerie = ?");
                        $stmt->execute([$user->__get('id') + 0, $serieId + 0]);
                    } else {
                        // sinon (si ce n'est pas le dernier episode
                        $stmt = $db->prepare("select * from user_serie_en_cours where idUser = ? and idSerie = ?");
                        $stmt->execute([$user->__get('id') + 0, $serieId + 0]);
                        $data = $stmt->fetch();
                        // si elle est deja dans les series en cours
                        if ($data) {
                            $numEpisodeDB = $data['numEpisode'];
                            // si l'epiosde courant est sup à l'episode deja vu
                            if ($numEpisode > $numEpisodeDB) {
                                // on modifie
                                $stmt = $db->prepare("update user_serie_en_cours set numEpisode = ? where idUser = ? and idSerie = ?");
                                $stmt->execute([$numEpisode, $user->__get('id') + 0, $serieId + 0]);
                            }// sinon, on ne fait rien
                        } else {
                            // sinon
                            // on l'ajoute dans les series en cours  avec l'episode courant
                            $stmt = $db->prepare("insert into user_serie_en_cours (idUser, idSerie, numEpisode) values (?, ?, ?)");
                            $stmt->execute([$user->__get('id') + 0, $serieId + 0, $numEpisode + 0]);
                        }

                    }
                }
            }


        }
        $html .= "<a class='text-2xl hover:text-red-600' href='index.php'>Retour page principale</a>";
        return $html;
    }
}