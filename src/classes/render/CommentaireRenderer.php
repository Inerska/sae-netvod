<?php

namespace Application\render;

use Application\video\Serie;

class CommentaireRenderer implements Renderer{

    private Serie $serie;

    public function __construct(Serie $serie){
        $this->serie = $serie;
    }

    public function render(): string
    {
        // on affiche le dernier commentaire
        if ($this->serie->nbCommentaires == 0) {
            // il faut regarder ou on est
            $html = <<<END
            <form action="?action=display-series-episode&serieId={$this->serie->id}&numEp={$this->serie->id}" method="post">
                <input type="hidden" name="serieId" value="{$id}">
                <input type="hidden" name="episodeId" value="{$numero}">
                <h3>Notez la série :</h3>
                <label><input type="radio" id="note_1" name="note" value="1">1</label><br>
                <label><input type="radio" id="note_2" name="note" value="2">2</label><br>
                <label><input type="radio" id="note_3" name="note" value="3">3</label><br>
                <label><input type="radio" id="note_4" name="note" value="4">4</label><br>
                <label><input type="radio" id="note_5" name="note" value="5">5</label><br>
                <h3>Commentez la série :</h3>
                <textarea name="commentaire" rows="10" cols="30"></textarea><br>
                <input type="submit" value="Noter">
            </form>
     END;
        }else{
            $commentaire = $this->serie->__get('commentaires')[0];
            $html = <<<END
                    <p>Commentaire de {$commentaire['email']} : </p>
                    <p>&emsp;{$commentaire['commentaire']}</p>
                    <p>&emsp;Note : {$commentaire['note']}/5</p>
                    <p><a href='?action=commentaires&serieId={$this->serie->id}'>Voir tous les commentaires</a></p>

                END;
        }


        return $html;
    }
}