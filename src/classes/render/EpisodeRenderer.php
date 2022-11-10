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


        if(isset($_GET['action'])){
            if ($_GET['action'] === 'display-series-episode'){
                $html = <<<END

                <div class="flex">
                        <div>
                            <h1 class="text-red-600 text-2xl font-bold'">Episode {$this->episode->numero} - {$this->episode->titre}</h1>
                            <video controls><source src='video/{$this->episode->file}' type='video/mp4'></video>
                            <p>Durée : {$this->episode->duree} secondes</p>
                            <p class='text-lg'>Resumé : {$this->episode->resume}</p>
                        </div>
                </div>
                END;

            }else{
                $html = <<<END
                        <a class="flex flex-col items-center w-2/6 bg-blue-100" href="index.php?action=display-series-episode&serieId={$this->episode->serieId}&numEp={$this->episode->numero}">
                                <h1 class="text-red-600 text-2xl font-bold'">Episode {$this->episode->numero} - {$this->episode->titre}</h1>
                                <video  class="h-58 w-40"><source src='video/{$this->episode->file}' type='video/mp4'></video>
                                <p>Durée : {$this->episode->duree} secondes</p>
                        </a>
                END;
            }
        }
        return $html;
    }
}