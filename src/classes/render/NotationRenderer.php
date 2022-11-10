<?php

namespace Application\render;

use Application\datalayer\factory\ConnectionFactory;
use Application\video\Serie;

class NotationRenderer implements Renderer{


    private Serie $serie;

    public function __construct(Serie $serie){
        $this->serie = $serie;
    }

    public function render(): string
    {
        $html = "";
        // si l'utilisateur co n'as pas mis de commentaire
        if (isset($_SESSION['loggedUser'])){
            $user = unserialize($_SESSION['loggedUser']);

            $db = ConnectionFactory::getConnection();
            $stmt = $db->prepare("select * from notation where idUser = ? and idSerie = ?");
            $stmt->execute([$user->id, $this->serie->id]);
            $data = $stmt->fetch();
            if (!$data) {
                // on lui propose d'en mettre un
                if ($_SERVER['REQUEST_METHOD'] == 'GET') {

                    $html .= <<<END
                        <div class="w-1/2">
                        <form action='?action=viewSerie&id={$this->serie->id}' method='post'>
                        <div class="w-2/6 bg-gray-100 flex flex-col items-center">
                        <h3>Notez la série</h3>
                        <div class="flex flex-row justify-between w-full">
                            <label><input type="radio" id="note_1" name="note" value="1">1</label><br>
                            <label><input type="radio" id="note_2" name="note" value="2">2</label><br>
                            <label><input type="radio" id="note_3" name="note" value="3">3</label><br>
                            <label><input type="radio" id="note_4" name="note" value="4">4</label><br>
                            <label><input type="radio" id="note_5" name="note" value="5">5</label><br>
                        </div>
                        
                        
                        <h3>Commentez la série</h3>
                        <textarea name="commentaire" rows="10" cols="30"></textarea><br>
                        <input class="w-full" type="submit" value="Noter">
                        </div>
                        </form>
                        </div>
                    END;
                }else if($_SERVER['REQUEST_METHOD'] == 'POST') {

                    if(isset($_POST['note']) && isset($_POST['commentaire']) ) {


                        $note = $_POST['note'];
                        $commentaire = $_POST['commentaire'];
                        $sql = "INSERT INTO `notation` (`idUser`, `idSerie`, `note`, `commentaire`) VALUES (?, ?, ?, ?)";
                        $stmt = $db->prepare($sql);
                        $stmt->execute([$user->id, $this->serie->id, $note, $commentaire]);

                        $st = $db->prepare("select note_moyenne, nombre_note from serie where id = ?");
                        $st->execute([$this->serie->id]);

                        $data = $st->fetch();
                        $nbNote = $data['nombre_note'];
                        $noteMoyenne = $data['note_moyenne'];

                        $st2 = $db->prepare("update serie set note_moyenne = ?, nombre_note = ? where id = ?");
                        // si la serie n'a pas encore de note
                        if ($nbNote == 0) {
                            $st2->execute([$note, $nbNote + 1, $this->serie->id]);
                        } else {
                            $st2->execute([($noteMoyenne + $note) / 2, $nbNote + 1, $this->serie->id]);
                        }

                        header("Location: ?action=viewSerie&id={$this->serie->id}");
                    }
                }
            }else{
                // sinon, on affiche sa note
                $html .= "<h1 class='text-red-600 text-2xl font-bold'>Vous avez noté cette série {$data['note']} / 5</h1>";

            }
        }else{
            $html .= "<p>Aucun utilisateur connecté</p>";
        }


        return $html;
    }
}