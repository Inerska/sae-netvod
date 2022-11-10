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
        if (isset($_GET['id'])) {


            $serieId = $_GET['id'];

            // cree la serie
            $serie = new Serie($serieId);
            //affiche la serie
            $renderer = new SerieRenderer($serie);

            $html = $renderer->render();



        }else{
            $html = "<p>Serie introuvable</p>";
            $html .= "<a class='text-2xl hover:text-red-600' href='index.php'>Retour page principale</a>";
        }


        $html .= "<br><br><a href='index.php' class='text-gray-900 dark:text-white'>Retour page principale</a>";
        return $html;
    }
}