<?php

namespace Application\render;
use Application\video\Episode;
use Application\datalayer\factory\ConnectionFactory;


class EpisodeNotationRenderer implements Renderer {

    private Episode $episode;

    public function __construct(Episode $e) {
        $this->episode = $e;

    }

    public function render(): string {
        $id = $this->episode->serieId;
        $numero = $this->episode->numero;
        $episodeRender = new EpisodeRenderer($this->episode);
        $html = $episodeRender->render();

        $db = ConnectionFactory::getConnection();
        $sql = "SELECT * FROM `notation` WHERE idUser = ? and idSerie = ?";
        $stmt = $db->prepare($sql);
        $idUser = unserialize($_SESSION['loggedUser'])->id;
        $stmt->execute([$idUser, $id]);
        $data = $stmt->fetch();
        if ($data){
            $html .= "<p>Vous avez déjà noté cette série {$data['note']}/5</p>";
        } else {
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $html .= <<<END
            <form action="?action=display-series-episode&serieId={$id}&episodeId={$numero}" method="post">
                <input type="hidden" name="serieId" value="{$id}">
                <input type="hidden" name="episodeId" value="{$numero}">
                <h3>Notez la série :</h3>
                <label><input type="radio" id="note_1" name="note" value="1">1</label><br>
                <label><input type="radio" id="note_2" name="note" value="2">2</label><br>
                <label><input type="radio" id="note_3" name="note" value="3">3</label><br>
                <label><input type="radio" id="note_4" name="note" value="4">4</label><br>
                <label><input type="radio" id="note_5" name="note" value="5">5</label><br>
                <h3>Commentez la série :</h3>
                <textarea name="commentaire" rows="10" cols="30"></textarea><br>
                <input type="submit" value="Noter">
            </form>
     END;
            } else if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $note = FILTER_SANITIZE_NUMBER_FLOAT($_POST['note']);
                $commentaire = $_POST['commentaire'];
                $sql = "INSERT INTO `notation` (`idUser`, `idSerie`, `note`, `commentaire`) VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([unserialize($_SESSION['loggedUser'])->id, $id, $note, $commentaire]);

                $st = $db->prepare("select note_moyenne, nombre_note from serie where idSerie = ?");
                $data = $st-execute($id);

                $st2 = $db->prepare("update serie set note_moyenne = ?, nombre_note = ? where idSerie = ?" );
                $st2->execute([ ($data['note_moyenne']+$note)/2,  ($data['nombre_note'])+1, $id]);

                $html .= "<p>Vous avez noté cette série $note / 5.</p>";
            }
        }
        return $html;
    }
}