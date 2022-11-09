<?php

namespace Application\render;

use Application\video\Serie;
use Application\render\Renderer;

class SerieRenderer implements Renderer {

    private Serie $serie;
    private string $rendered;

    public function __construct(Serie $s) {
        $this->serie = $s;

    }

    public function render(): string
    {
        if ($this->serie->id == 0){
            $html ="<p>La série n'existe pas.</p>";
        } else {
            $html = "<div class = 'serie' >";
            $seriesCard = new SeriesCardRenderer($this->serie->image, $this->serie->titre, $this->serie->id);

            $html .= $seriesCard->render();

            $html .= "<p>Genres : ";
            foreach ($this->serie->genre as $g) {
                $html .= $g . ", ";
            }
            $html .= "</p>";
            $html .= "<p>Public visé : ";
            foreach ($this->serie->publicVise as $p) {
                $html .= $p . ", ";
            }

           $html .= <<<END
                </p>
                <p>Descriptif : {$this->serie->descriptif}</p>
                <p>Année : {$this->serie->annee}</p>
                <p>Date ajout : {$this->serie->dateAjout}</p>
        END;
            if ($this->serie->moyenne == 0){
                $html .= "<p>La série n'a pas encore été notée.</p>";
            } else {
                $html .= "<p>Note moyenne : {$this->serie->moyenne}</p>";
            }
            $html .= <<<END
                <p>Nombre d'épisodes : {$this->serie->nbEpisodes}</p>
                <p>Liste des épisodes : </p>
                <div class="flex flex-wrap">
        END;

            foreach ($this->serie->episodes as $episode) {
                $e = new EpisodeRenderer($episode);
                $html .= $e->render();

            }
            $html .= "</div>";

            // on affiche le dernier commentaire
            if ($this->serie->nbCommentaires == 0) {
                $html .= "<p>La série n'a pas encore été commentée.</p>";
            }else{
                $commentaire = $this->serie->__get('commentaires')[0];
                $html .= <<<END
                    <p>Commentaire de {$commentaire['email']} : </p>
                    <p>&emsp;{$commentaire['commentaire']}</p>
                    <p>&emsp;Note : {$commentaire['note']}/5</p>
                END;
            }

            $html .= "<p><a href='?action=commentaires&serieId={$this->serie->id}'>Voir tous les commentaires</a></p>";
        }
        return $html;
    }
}