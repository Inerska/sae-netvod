<?php

namespace Application\render;
use Application\video\Episode;
use Application\datalayer\factory\ConnectionFactory;


class EpisodeRenderer implements Renderer {

    private Episode $episode;

    public function __construct(Episode $e) {
        $this->episode = $e;

    }

    public function render(): string {


        if($_GET['action']){
            if ($_GET['action'] === 'display-series-episode'){
                $html = <<<END

                <div class="flex">
                        <div>
                            <h1 class="text-red-600 text-2xl font-bold'">Episode {$this->episode->numero} - {$this->episode->titre}</h1>
                            <video><source src='video/{$this->episode->file}' type='video/mp4'></video>
                            <p>Durée : {$this->episode->duree} secondes</p>
                            <p class='text-lg'>Resumé : {$this->episode->resume}</p>
                        </div>
                </div>
                END;

            }else{
                $html = <<<END
                <div class="flex">
                    <a href="index.php?action=display-series-episode&serieId={$this->episode->serieId}&episodeId={$this->episode->numero}">
                        <div>
                            <h1 class="text-red-600 text-2xl font-bold'">Episode {$this->episode->numero} - {$this->episode->titre}</h1>
                            <video class="h-58 w-40"><source src='video/{$this->episode->file}' type='video/mp4'></video>
                            <p>Durée : {$this->episode->duree} secondes</p>
                        </div>
                    </a>
                </div>
                END;
            }
        }


        return $html;
    }

}