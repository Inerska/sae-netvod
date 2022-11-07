<?php

namespace Application\render;

use Application\video\Serie;
use iutnc\deefy\render\Renderer;

class SerieRenderer implements Renderer {

    private Serie $serie;
    private string $rendered;

    public function __construct(Serie $s) {
        $this->serie = $s;

    }

    public function render(): string
    {
        $html = "<div class = 'serie' >".
                "<h3> {$this->serie->titre} </h3>".
                "<h3>{$this->serie->genre}</h3>".
                "<h3>{$this->serie->publicVise}</h3>".
                "<h3>{$this->serie->descriptif}</h3>".
                "<h3>{$this->serie->annee}</h3>".
                "<h3>{$this->serie->dateAjout}</h3>".
                "<h3>{$this->serie->nbEpisodes}</h3>".
                "</div>";
        return $html;
    }
}