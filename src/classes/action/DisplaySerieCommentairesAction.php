<?php

namespace Application\action;

use Application\video\Serie;

class DisplaySerieCommentairesAction extends Action
{

    public function execute(): string
    {

        $serieId = $_GET['serieId'];
        $serie = new Serie($serieId);
        $html = "<h1>Commentaires - {$serie->titre}</h1>";
        $html .= "<ul>";
        foreach ($serie->commentaires as $commentaire) {
            $html .= <<<HTML
            <li>
                <p>{$commentaire['email']} : {$commentaire['note']}/5</p>
                <p>\t{$commentaire['commentaire']}</p>
            </li>
HTML;
        }
        if ($serie->nbCommentaires == 0) {
            $html .= "<il>La série n'a pas encore été commentée.</il>";
        }
        $html .= "</ul>";

        $html .= "<a href='?action=viewSerie&id={$serieId}'>Retour à la série</a>";
        return $html;
    }
}