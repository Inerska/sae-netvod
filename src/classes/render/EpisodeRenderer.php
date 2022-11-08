<?php

namespace Application\render;
use Application\video\Episode;


class EpisodeRenderer implements Renderer {

    private Episode $episode;

    public function __construct(Episode $e) {
        $this->episode = $e;

    }

    public function render(): string {
        $html = <<<END
                <h3>Episode {$this->episode->numero} - {$this->episode->titre}</h3>
                <p>Durée : {$this->episode->duree} secondes</p>
         END;
        return $html;
    }

    public function longRender():String{
        $html = $this->render();
        $html .= "<p>Resumé : {$this->epiosde->resume}</p>";

        return $html;
    }
}