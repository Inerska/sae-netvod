<?php

namespace Application\action;

use Application\render\SerieRenderer;
use Application\video\Serie;

class ViewSerieAction extends Action
{

    public function execute(): string
    {
        $html ='';
        if(isset($_GET['id'])) {
            $serie = new Serie($_GET['id']);
            $serieR = new SerieRenderer($serie);
            $html .= $serieR->render();
        } else {
            $html .= "Aucune série sélectionnée";
        }
        $html .= "<a href='index.php' class='text-gray-900 dark:text-white'>Retour page principale</a>";

        return $html;
    }
}