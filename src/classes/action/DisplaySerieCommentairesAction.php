<?php

namespace Application\action;

use Application\video\Serie;

class DisplaySerieCommentairesAction extends Action
{

    public function execute(): string
    {

        $serieId = $_GET['serieId'];
        $serie = new Serie($serieId);
        $html = "<div class='commentaires'>".
            "<img class='h-58 w-40' src='{$serie->image}' alt='image de la série' /><br>".
            "<div id='list-commentaires'><h1>Commentaires - {$serie->titre}</h1>".
             "<ul><br>";
        foreach ($serie->commentaires as $commentaire) {
            $html .= <<<HTML
                <li>
                    <p>Commentaire de {$commentaire['email']} : </p>
                    <p>&emsp;{$commentaire['commentaire']}</p>
                    <p>Note : {$commentaire['note']}/5</p>
                </li><br>
            HTML;
        }
        if ($serie->nbCommentaires == 0) {
            $html .= "<il>La série n'a pas encore été commentée.</il>";
        }
        $html .= "</ul></div>";

        $html .= "<div id='retour-commentaire'><a href='?action=viewSerie&id={$serieId}'>Retour à la série</a></div></div>";
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