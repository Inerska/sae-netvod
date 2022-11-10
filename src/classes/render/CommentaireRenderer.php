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
            $html .= "<h1 class='text-red-600 text-2xl font-bold'>Il n'y a aucun commentaire pour le moment</h1>";

        }else{
            $html .= "<h1 class='text-red-600 text-2xl font-bold'>Commentaires</h1>";
            $commentaire = $this->serie->__get('commentaires')[0];

            $email = htmlentities($commentaire['email']);
            $commentaireC = htmlentities($commentaire['commentaire']);
            $note = htmlentities($commentaire['note']);

            $html .= <<<END
                    <p class="dark:text-white">Commentaire de {$email}</p>
                    <p class="dark:text-white">&emsp;{$commentaireC}</p>
                    <p class="dark:text-white">&emsp;Note : {$note}/5</p>
                    <p><a class="hover:text-red-600 dark:text-white" href='?action=commentaires&serieId={$this->serie->id}'>Voir tous les commentaires</a></p>

                END;
        }


        return $html;
    }
}