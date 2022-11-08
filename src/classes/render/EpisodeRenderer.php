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
                "<a href='index.php?action=display-series-episode&episodeId={$this->episode->id}&serieId={$this->episode->serieId}'>Episode {$this->episode->numero} - {$this->episode->titre} </a>".
                "<p>Durée : {$this->episode->duree} secondes</p>" .
                //"<img src='{$this->episode->image}' alt='image de l'episode' />".
                "</div>";
        return $html;
    }
}