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
            $html = "<div class = 'serie' >".
                "<img src='{$this->serie->image}' alt='image de la série' />".
                "<h3>Titre : {$this->serie->titre} </h3>";
            $html .= "<p>Genres : ";
            foreach ($this->serie->genre as $g) {
                $html .= $g . ", ";
            }
            $html .= "</p>";
            $html .= "<p>Public visé : ";
            foreach ($this->serie->publicVise as $p) {
                $html .= $p . ", ";
            }
            $html .= "</p>";
            $html .= "<p>Descriptif : {$this->serie->descriptif}</p>".
                "<p>Année : {$this->serie->annee}</p>".
                "<p>Date ajout : {$this->serie->dateAjout}</p>".
                "<p>Note Moyenne : {$this->serie->moyenne}</p>".
                "<p>Nombre d'épisodes : {$this->serie->nbEpisodes}</p>".
                "<p>Liste des épisodes : </p>".
                "</div>";
            foreach ($this->serie->episodes as $episode) {
                $e = new EpisodeRenderer($episode);
                $html .= $e->render();
            }
        }
        return $html;
    }
}