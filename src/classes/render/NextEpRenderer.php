<?php

namespace Application\render;

use Application\datalayer\factory\ConnectionFactory;
use Application\video\Episode;
use Application\video\Serie;

class NextEpRenderer implements Renderer {

    private Serie $serie;

    public function __construct(Serie $s) {
        $this->serie = $s;

    }

    public function render(): string{
        // on affiche l'epiosde suivant a regarder si la serie est en cours
        // si un utilisateur est co
        $html = "";
        if (isset($_SESSION['loggedUser'])) {
            $user = unserialize($_SESSION['loggedUser']);
            $db = ConnectionFactory::getConnection();
            $stmt = $db->prepare("select numEpisode from user_serie_en_cours where idUser = ? and idSerie = ?");
            $stmt->execute([$user->id + 0, $this->serie->id + 0]);
            $data = $stmt->fetch();

            // si la serie est en cours
            if ($data) {
                // si l'episode n'est pas le dernier
                $nbEp = $this->serie->nbEpisodes;
                $numEpSuiv = $data['numEpisode'] + 1;
                if ($numEpSuiv <= $nbEp) {
                    $html .= "<div class='w-1/2'>";
                    $html .= "<p class='dark:text-white'>Continuez votre lecture </p>";
                    // Reprendre le prochain épisode
                    $episodeRenderer =  new EpisodeCardRender(new Episode($this->serie->id, $numEpSuiv));
                    $html .= $episodeRenderer->render();
                    $html .= "</div>";
                }
            }
        }
        return $html;
    }
}