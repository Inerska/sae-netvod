<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;

class AjouterEpisodeAction extends Action
{

    public function execute(): string
    {
        if(!isset($_SESSION['loggedUser'])) {
            header('Location: index.php');
            exit();
        }

        $html="";
        $db = ConnectionFactory::getConnection();
        $stmt = $db->prepare("select max(id) as maxId from serie");
        $stmt->execute();
        $maxId = $stmt->fetch()['maxId'];


        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['episodeAdd']) && !$_GET['episodeAdd']){
            $html .= <<<HTML
            <form action="?action=add-episodes&episodeAdd=1" method="post">
               <label for="id">Choisir la série</label><br>
               <input type="number" name="id" id="id" min="1" max="$maxId" value="1" required><br>
               <input type="submit" value="Choisir">
</form>
HTML;
        } else if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['episodeAdd']) && $_GET['episodeAdd'] == 1){
            $id = $_POST['id'];
            $html .= <<<HTML
<div class="container-add-episode">
            <form action="?action=add-episodes&episodeAdd=2" method="post">
              <label for="nom">Titre de l'épisode</label>
                <input type="text" name="nom" id="nom" required>
                <label for="resume">Résumé</label>
              <textarea name="resume" id="resume" cols="30" rows="10" required></textarea>
              <label for="duree">Duree de l'episode</label>
              <input type="number" name="duree" min="0" id="duree">
                <label for="file">File</label>
                <input type="text" name="file" id="file">  
                <input type="hidden" name="id" value="$id">
               <input type="submit" value="Ajouter">
</form>
</div>
HTML;

        } else if(isset($_GET['episodeAdd']) && $_GET['episodeAdd'] == 2){
            $newNum = 1;
            $stmt = $db->prepare("select * from episode where serie_id = ?");
            $stmt->execute([$_POST['id']]);
            $data1 = $stmt->fetch();
            if ($data1) {
                $stmtNum = $db->prepare("select max(numero)+1 as newNum from episode where serie_id = ?");
                $stmtNum->execute([$_POST['id']]);
                $data2 = $stmtNum->fetch();
                if ($data2) {
                    $newNum = $data2['newNum'];
                }
            }
            $stmt1 = $db->prepare("select max(id)+1 as maxId from episode");
            $stmt1->execute();
            $maxId = $stmt1->fetch()['maxId'];
            if($maxId ==null) $maxId = 1;
            $nom = $_POST['nom'];
            $resume = $_POST['resume'];
            $duree = $_POST['duree'];
            $file = $_POST['file'];
            $id = $_POST['id'];
            $stmt = $db->prepare("insert into episode values (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$maxId, $newNum, $nom, $resume, $duree, $file, $id]);

        }
        $html .= <<<HTML
<style>
    .container-add-episode {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    form input, form textarea {
        margin: 10px;
        border: 1px solid black;
        background: aliceblue;
    }
HTML;

        return $html;
    }
}