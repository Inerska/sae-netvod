<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\render\SeriesCardRenderer;

class RetirerSeriesAction extends Action
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


        if ($_SERVER['REQUEST_METHOD'] == 'GET'){
            $html .= <<<HTML
            <div class="container">
HTML;
            $stmt = $db->prepare("select * from serie");
            $stmt->execute();
            while ($result = $stmt->fetch()) {
                $seriesCard = new SeriesCardRenderer($result["img"], $result["titre"], $result["id"]);
                $html .= $seriesCard->render();
            }
            $html .= <<<HTML
            </div>
            <style>
                .container {
                    display: flex;
                    flex-direction: row;
                    flex-wrap: wrap;
                    justify-content: space-between ;
                }
</style>
HTML;


            $html .= <<<HTML
            <form action="?action=remove-series" method="post">
                <label for="nom">Id de la série à supprimer</label>
                <input type="number" name="id" id="id" min="1" max="$maxId" required>
                <input type="submit" value="Retirer">
</form>
<style>
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
</style>
HTML;

        } else {
            $stmt = $db->prepare("delete from serie where id = ?");
            $stmt->execute([$_POST['id']]);
            $stmt = $db->prepare("delete from episode where serie_id = ?");
            $stmt->execute([$_POST['id']]);
            $stmt = $db->prepare("delete from notation where idSerie = ?");
            $stmt->execute([$_POST['id']]);
            $stmt = $db->prepare("delete from serie_genre where idSerie = ?");
            $stmt->execute([$_POST['id']]);
            $stmt = $db->prepare("delete from user_serie_en_cours where idSerie = ?");
            $stmt->execute([$_POST['id']]);
            $stmt = $db->prepare("delete from user_serie_vu where idSerie = ?");
            $stmt->execute([$_POST['id']]);
            $stmt = $db->prepare("delete from user_serie_pref where idSerie = ?");
            $stmt->execute([$_POST['id']]);
            $stmt = $db->prepare("delete from serie_type where idSerie = ?");
            $stmt->execute([$_POST['id']]);
            $stmt->closeCursor();
        }
        return $html;
    }
}