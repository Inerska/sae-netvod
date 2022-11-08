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
        if (isset($_GET['id'])){
            $id = $_GET['id'];
        }else if(isset($_GET['serieId'])){
            $id = $_GET['serieId'];
        }else{
            // on recupere l'id de la serie sur laquelle on clique
            $bd = ConnectionFactory::getConnection();
            $query = "select serie_id from episode where id = ?";
            $stmt = $bd->prepare($query);
            $id = $stmt->execute([$this->episode->id]);
        }
        $html = <<<END
                <h3><a href="index.php?action=display-series-episode&serieId={$id}&episodeId={$this->episode->numero}">Episode {$this->episode->numero} - {$this->episode->titre}</a></h3>
                <p>Durée : {$this->episode->duree} secondes</p>
         END;
        return $html;
    }

    public function longRender():String{
        $html = $this->render();
        $html .= "<p>Resumé : {$this->episode->resume}</p>";

        return $html;
    }
}