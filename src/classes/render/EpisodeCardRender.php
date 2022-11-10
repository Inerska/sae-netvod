<?php

namespace Application\render;

use Application\video\Episode;

class EpisodeCardRender implements Renderer {

    private Episode $episode;

    public function __construct(Episode $e) {
        $this->episode = $e;

    }

    public function render(): string{
        $html = <<<END
                        <a class="flex flex-col bg-gray-100 items-center w-2/6 dark:bg-gray-700" href="index.php?action=display-series-episode&numEp={$this->episode->numero}&serieId={$this->episode->serieId}">
                                <h1 class="text-red-600 text-2xl font-bold'">Episode {$this->episode->numero} - {$this->episode->titre}</h1>
                                <video  class="h-58 w-40"><source src='video/{$this->episode->file}' type='video/mp4'></video>
                                <p>Durée : {$this->episode->duree} secondes</p>
                        </a>
                END;

        return $html;
    }
}