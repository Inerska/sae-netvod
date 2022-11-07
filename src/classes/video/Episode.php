<?php

namespace Application\video;
use Application\datalayer\factory\ConnectionFactory;
use Application\exception\EpisodeNotFoundException;

class Episode {
    protected int $id;
    protected int $numero;
    protected string $titre;
    protected string $resume;
    protected int $duree;
    protected string $file;
    protected int $serieId;

    public function __construct(int $id, int $numero){
        ConnectionFactory::setConfig("config.ini");
        $conn = ConnectionFactory::getConnection();
        $sql = "SELECT * FROM episode WHERE id = ? AND numero = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id,$numero]);
        $data = $stmt->fetch();
        $this->id = $data['id'];
        $this->numero = $data['numero'];
        $this->titre = $data['titre'];
        $this->resume = $data['resume'];
        $this->duree = $data['duree'];
        $this->file = $data['file'];
        $this->serieId = $data['serie_id'];
        $stmt->closeCursor();
    }

    public function __get(string $attrname) : mixed
    {
        if (!property_exists($this, $attrname)) throw new InvalidPropertyNameException("Invalid property name : $attrname");
        return $this->$attrname;
    }


}