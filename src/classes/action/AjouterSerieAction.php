<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;

class AjouterSerieAction extends Action
{

    public function execute(): string
    {
        $html="";

        if ($_SERVER['REQUEST_METHOD'] == 'GET'){
            $html .= <<<HTML
            <form action="?action=add-series" method="post">
                <label for="nom">Nom de la série</label>
                <input type="text" name="nom" id="nom" required>
                <label for="nbEpisodes">Nombre d'épisodes</label>
                <input type="number" name="nbEpisodes" min="0" max="99" id="nbEpisodes">
                <label for="annee">Année de sortie</label>
                <input type="number" name="annee" id="annee" required>
                <label for="description">Description</label>
                <textarea name="description" id="description" cols="30" rows="10" required></textarea>
                <label for="image">Image</label>
                <input type="text" name="image" id="image">
                <input type="submit" value="Ajouter">
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
            $db = ConnectionFactory::getConnection();
            $stmt = $db->prepare("select CURRENT_DATE() from dual");
            $stmt->execute();
            $dateAjout = $stmt->fetch()['CURRENT_DATE()'];
            $stmt = $db->prepare("select max(id)+1 as newId from serie");
            $stmt->execute();
            $idSerie = $stmt->fetch()['newId'];
            $date = $_POST['annee'];
            $titre = $_POST['nom'];
            $nbEpisodes = $_POST['nbEpisodes'];
            $description = $_POST['description'];
            $image = $_POST['image'];
            $stmt = $db->prepare("insert into serie values (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$idSerie, $titre, $description, $image,$date, $dateAjout]);
            $stmt->closeCursor();
        }
        return $html;
    }
}