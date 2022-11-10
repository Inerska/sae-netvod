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
        if (!isset($_GET['numEp']) || !isset($_GET['serieId'])) {
            $html = <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Erreur lors de l'affichage</h1>
                                        <a href='index.php' class="text-gray-900 dark:text-white font-sm text-lg">Retour page principale</a>
                                    </div>
                                </div>
                                END;
        } else {
            $serieId = $_GET['serieId'];
            $numEpisode = $_GET['numEp'];

            $episode = new Episode($serieId+0, $numEpisode+0);
            $renderer = new EpisodeRenderer($episode);


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

            // on affiche la page
            $html = $renderer->render();



        }
        $html .= "<a class='text-2xl hover:text-red-600' href='index.php'>Retour page principale</a>";
        return $html;
    }
}