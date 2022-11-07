<?php

namespace iutnc\deefy\render;

use Application\datalayer\factory\ConnectionFactory;

class Serie {
    protected int $id;
    protected string $titre;
    protected string $descriptif;
    protected string $image;
    protected string $annee;
    protected string $date_ajout;
    protected array $episodes;

    public function __construct(int $id){
        ConnectionFactory::setConfig("config.ini");
        $conn = ConnectionFactory::getConnection();
        $sql = "SELECT * FROM serie WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch();
    }
}
