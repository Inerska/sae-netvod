<?php

namespace Application\render;
use Application\datalayer\factory\ConnectionFactory;
use Application\video\Episode;


class EpisodeRenderer implements Renderer {

    private Episode $episode;

    public function __construct(Episode $e) {
        $this->episode = $e;

    }

    public function render(): string {

        $html = <<<END
                <h1 class="text-red-600 text-2xl font-bold'"><a href="index.php?action=display-series-episode&serieId={$this->episode->serieId}&episodeId={$this->episode->numero}">Episode {$this->episode->numero} - {$this->episode->titre}</a></h1>
                <p>Durée : {$this->episode->duree} secondes</p>
         END;
        return $html;
    }

    public function longRender():String{
        $html = $this->render();
        $html .= "<p class='text-lg'>Resumé : {$this->episode->resume}</p>";
        $html .= "<video><source src='video/{$this->episode->file}' type='video/mp4'></video>";

        return $html;
    }

}