<?php

namespace Application\action;

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

        }
        $html .= "<a href='index.php'>Retour page principale</a>";
        return $html;
    }
}