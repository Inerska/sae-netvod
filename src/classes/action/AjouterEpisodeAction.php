<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;

class AjouterEpisodeAction extends Action
{

    public function execute(): string
    {
        $html="";

        if ($_SERVER['REQUEST_METHOD'] == 'GET'){
            $html .= <<<HTML
            <form action="?action=add-series" method="post">
               <select name="serie" id="serie"></select>
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