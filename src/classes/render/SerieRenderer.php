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
        $html = "<div class = 'serie' >".
                "<h3>Titre : {$this->serie->titre} </h3>".
                "<p>Genre : {$this->serie->genre}</p>".
                "<p>Public visé : {$this->serie->publicVise}</p>".
                "<p>Descriptif : {$this->serie->descriptif}</p>".
                "<p>Année : {$this->serie->annee}</p>".
                "<p>Date ajout : {$this->serie->dateAjout}</p>".
                "<p>Nombre d'épisodes : {$this->serie->nbEpisodes}</p>".
                "<p>Liste des épisodes : </p>".
                "</div>";
        foreach ($this->serie->episodes as $episode) {
            $e = new EpisodeRenderer($episode);
            $html .= $e->render();
        }
        return $html;
    }
}