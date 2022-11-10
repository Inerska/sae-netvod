<?php

namespace Application\render;

use Application\datalayer\factory\ConnectionFactory;
use Application\video\Serie;

class CommentaireRenderer implements Renderer{

    private Serie $serie;

    public function __construct(Serie $serie){
        $this->serie = $serie;
    }

    public function render(): string{

        $html = "";


        $this->serie = new Serie($this->serie->id);

        // on affiche le premier comm

        // on affiche le dernier commentaire
        if ($this->serie->nbCommentaires == 0) {
            $html.= "<p>Aucun commentaire pour le moment</p>";
        }else{
            $commentaire = $this->serie->__get('commentaires')[0];
            $html .= <<<END
                    <p>Commentaire de {$commentaire['email']} : </p>
                    <p>&emsp;{$commentaire['commentaire']}</p>
                    <p>&emsp;Note : {$commentaire['note']}/5</p>
                    <p><a href='?action=commentaires&serieId={$this->serie->id}'>Voir tous les commentaires</a></p>

                END;
        }


        return $html;
    }
}