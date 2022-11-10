<?php

namespace Application\render;

use Application\video\Episode;
use Application\datalayer\factory\ConnectionFactory;
use Application\video\Serie;


class EpisodeRenderer implements Renderer
{

    private Episode $episode;

    public function __construct(Episode $e)
    {
        $this->episode = $e;

    }

    public function render(): string
    {


        $serie = new Serie($this->episode->serieId);
        $commentaireRenderer = new CommentaireRenderer($serie);
        $notationRenderer = new NotationRenderer($serie);
        // on affiche nesuite le prochaine episode
        $nextEpRenderer = new NextEpRenderer($serie);

        $html = <<<END

                        <div>
                            <video controls class="w-full"><source src='video/{$this->episode->file}' type='video/mp4'></video>
                            <h1 class="text-red-600 text-2xl font-bold'">Episode {$this->episode->numero} - {$this->episode->titre}</h1>
                            <p class='text-lg'>{$this->episode->resume} - {$this->episode->duree} secondes</p>
                        </div>
                        {$commentaireRenderer->render()}
                        {$notationRenderer->render()}
                        {$nextEpRenderer->render()}

                END;


        return $html;
    }
}