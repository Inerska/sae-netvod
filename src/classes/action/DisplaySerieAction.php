<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\render\SerieRenderer;
use Application\video\Serie;

class DisplaySerieAction extends Action
{

    public function execute(): string
    {
        if(!isset($_SESSION['loggedUser'])) {
            header('Location: index.php');
            exit();
        }

        // get l'id de la serie a afficher
        if (isset($_GET['id'])) {


            $serieId = $_GET['id'];

            // cree la serie
            $serie = new Serie((int)$serieId);
            //affiche la serie
            $renderer = new SerieRenderer($serie);

            $html = $renderer->render();



        }else{
            $html = <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Serie introuvable</h1>
                                        <a href='index.php' class="text-gray-900 dark:text-white font-sm text-lg">Retour page principale</a>
                                    </div>
                                </div>
                                END;
        }

        return $html;
    }
}