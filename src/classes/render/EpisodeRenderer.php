<?php

namespace Application\render;
use Application\video\Episode;


class EpisodeRenderer implements Renderer {

    private Episode $episode;

    public function __construct(Episode $e) {
        $this->episode = $e;

    }

    public function render(): string {
        $html = "<div class = 'episode' >".
                "<h3>Episode {$this->episode->numero} - {$this->episode->titre} </h3>".
                "<p>Durée : {$this->episode->duree} secondes</p>" .
                //"<img src='{$this->episode->image}' alt='image de l'episode' />".
                "</div>";
        return $html;
    }
}