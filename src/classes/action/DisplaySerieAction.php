<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\render\SerieRenderer;
use Application\video\Serie;

class DisplaySerieAction extends Action
{

    public function execute(): string
    {
        // get l'id de la serie a afficher
        $serieId = $_GET['id'];

        // cree la serie
        $serie = new Serie($serieId);
        //affiche la serie
        $renderer = new SerieRenderer($serie);

        $html = $renderer->render();


        // si un utilisateur est co
        if (isset($_SESSION['loggedUser'])) {
            $user = unserialize($_SESSION['loggedUser']);
            $db = ConnectionFactory::getConnection();
            $stmt = $db->prepare("select numEpisode from user_serie_en_cours where idUser = ? and idSerie = ?");
            $stmt->execute([$user->__get('id') + 0, $serieId + 0]);
            $data = $stmt->fetch();

            // si la serie est en cours
            if ($data) {
                // si l'episode n'est pas le dernier
                $nbEp = $serie->__get('nbEpisodes');
                $numEpSuiv = $data['numEpisode'] + 1;
                if ($numEpSuiv <= $nbEp) {
                    // on propose de regarder l'episode en cours
                    $html .= "<a href='?action=display-series-episode&serieId=$serieId&episodeId=$numEpSuiv'>La serie est actuellement en cours, voullez-vous regarder le prochaine épisode ? </a>";
                }
            }
        }


        $html .= "<br><br><a href='index.php' class='text-gray-900 dark:text-white'>Retour page principale</a>";
        return $html;
    }
}