<?php

namespace Application\render;

use Application\datalayer\factory\ConnectionFactory;
use Application\video\Episode;
use Application\video\Serie;

class SerieRenderer implements Renderer {

    private Serie $serie;

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
            if ($this->serie->nbCommentaires == 0){
                $html .= "<p>La série n'a pas encore été notée.</p>";
            } else {
                $html .= "<p>Note moyenne : {$this->serie->moyenne}</p>";
            }
            $html .= <<<END
                <p>Nombre d'épisodes : {$this->serie->nbEpisodes}</p>
                <p>Liste des épisodes : </p>
                <div class="flex flex-wrap">
        END;

            // on affiche les episodes de la serie
            foreach ($this->serie->episodes as $episode) {
                $e = new EpisodeCardRender($episode);
                $html .= $e->render();

            }
            $html .= "</div>";

            // on affiche le prochiane epiosde a regarder (si la serie est en cours)
            $nextEpRenderer = new NextEpRenderer($this->serie);
            $html .= $nextEpRenderer->render();




            // on affiche la note de l'utilisateur
            $notationRenderer = new NotationRenderer($this->serie);
            $html .= $notationRenderer->render();
            // on affiche les derniers comms
            $commentaireRenderer = new CommentaireRenderer($this->serie);
            $html .= $commentaireRenderer->render();

        }
        return $html;
    }
}