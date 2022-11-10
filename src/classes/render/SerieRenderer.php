<?php

namespace Application\render;

use Application\datalayer\factory\ConnectionFactory;
use Application\video\Episode;
use Application\video\Serie;

class SerieRenderer implements Renderer {

    private Serie $serie;

    public function __construct(Serie $s) {
        $this->serie = $s;

    }

    public function render(): string
    {
        if ($this->serie->id == 0){
            $html = <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">La série n'existe pas</h1>
                                        <a href='index.php' class="text-gray-900 dark:text-white font-sm text-lg">Retour page principale</a>
                                    </div>
                                </div>
                                END;
            } else {
            $html = "<div class = 'serie' >";
            $seriesCard = new SeriesCardRenderer($this->serie->image, $this->serie->titre, $this->serie->id, $this->serie->annee);

            $html .= $seriesCard->render();

            $html .= "<p class='dark:text-white'>Genres : ";
            foreach ($this->serie->genre as $g) {
                $html .= $g . ", ";
            }
            $html .= "</p>";
            $html .= "<p class='dark:text-white'>Public visé : ";
            foreach ($this->serie->publicVise as $p) {
                $html .= $p . ", ";
            }

           $html .= <<<END
                </p>
                <p class="dark:text-white">Descriptif : {$this->serie->descriptif}</p>
                <p class="dark:text-white">Année : {$this->serie->annee}</p>
                <p class="dark:text-white">Date ajout : {$this->serie->dateAjout}</p>
        END;
            if ($this->serie->nbCommentaires == 0){
                $html .= "<p class='dark:text-white'>La série n'a pas encore été notée.</p>";
            } else {
                $html .= "<p class='dark:text-white'>Note moyenne : {$this->serie->moyenne}</p>";
            }
            $html .= <<<END
                <p class="dark:text-white">Nombre d'épisodes : {$this->serie->nbEpisodes}</p>
                <p class="dark:text-white">Liste des épisodes : </p>
                <div class="flex flex-wrap">
        END;

            // on affiche les episodes de la serie
            foreach ($this->serie->episodes as $episode) {
                $e = new EpisodeCardRender($episode);
                $html .= $e->render();

            }
            $html .= "</div>";


            // on affiche les derniers comms
            $commentaireRenderer = new CommentaireRenderer($this->serie);
            $html .= $commentaireRenderer->render();



            // on affiche la note de l'utilisateur
            $notationRenderer = new NotationRenderer($this->serie);
            // on affiche le prochiane epiosde a regarder (si la serie est en cours)
            $nextEpRenderer = new NextEpRenderer($this->serie);

            $html .= <<<END
                    {$notationRenderer->render()}
                    {$nextEpRenderer->render()}

            END;








        }
        return $html;
    }
}