<?php

namespace Application\action;

use Application\render\SerieRenderer;
use Application\video\Serie;

class DisplaySerieAction extends Action
{

    public function execute(): string{
        // get l'id de la serie a afficher
        $serieId = $_GET['id'];

        // cree la serie
        $serie = new Serie($serieId+0);
        //affiche la serie
        $renderer = new SerieRenderer($serie);

        $html = $renderer->render();

        return $html;
    }
}