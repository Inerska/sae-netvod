<?php

namespace Application\action;

use Application\video\Serie;

class DisplaySerieCommentairesAction extends Action
{

    public function execute(): string
    {
        if (!isset($_SESSION['loggedUser'])) {
            header('Location: index.php');
            exit();
        }

        if (!isset($_SESSION['loggedUser'])) {
            header('Location: index.php');
            exit();
        }

        if(isset($_GET['serieId'])) {


            $serieId = $_GET['serieId'];
            $serie = new Serie($serieId);

            if ($serie->id == 0) {
                $html = <<<END
                <div class="flex justify-center items-center flex-col h-screen pb-72">
                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Serie introuvable</h1>
                        <a href='index.php' class="text-gray-900 dark:text-white font-sm text-lg">Retour page principale</a>
                    </div>
                </div>
                END;
            } else {
                $html = "<div class='commentaires'>" .
                    "<img class='h-58 w-40' src='{$serie->image}' alt='image de la série' /><br>" .
                    "<div id='list-commentaires'><h1>Commentaires - {$serie->titre}</h1>" .
                    "<ul><br>";
                foreach ($serie->commentaires as $commentaire) {

                    $auteur = htmlentities($commentaire['email']);
                    $commentaireC = htmlentities($commentaire['commentaire']);
                    $note = htmlentities($commentaire['note']);

                    $html .= <<<HTML
                <li>
                    <p>Commentaire de {$auteur} : </p>
                    <p>&emsp;{$commentaireC}</p>
                    <p>Note : {$note}/5</p>
                </li><br>
            HTML;
                }
                if ($serie->nbCommentaires == 0) {
                    $html .= "<il>La série n'a pas encore été commentée.</il>";
                }
                $html .= "</ul></div>";

                $html .= "<div id='retour-commentaire'><a href='?action=viewSerie&id={$serieId}'>Retour à la série</a></div></div>";
            }


        }else{
            $html = "Erreur lors du chargement des commentaire";
        }
        $html .= <<<HTML
    <style>
            h1 {
                text-align: center;
                text-decoration: underline;
            }
            /*img{*/
            /*    width: 200px;*/
            /*    height: 300px;*/
            /*}*/
            .commentaires {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            #list-commentaires {
                display: flex;
                flex-direction: column;
                align-items: center;
                background: #f9f9fc;
                padding: 20px;
                margin: 20px;
                border-radius: 20px;

            }
            #list-commentaires ul {
                list-style-type: disc;
            }
            #retour-commentaire {
                background: #222321;
                padding: 5px;
                border: 1px solid black;
                border-radius: 5px;
            }
            #retour-commentaire a {
                color: red;
                text-decoration: none;
            } 
</style>
HTML;

        return $html;
    }
}