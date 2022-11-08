<?php

namespace Application\video;
use Application\datalayer\factory\ConnectionFactory;
use Application\exception\EpisodeNotFoundException;
use Application\exception\InvalidPropertyNameException;

class Episode {
    protected int $id;
    protected int $numero;
    protected string $titre;
    protected string $resume;
    protected int $duree;
    protected string $file;
    protected int $serieId;

    public function __construct(int $id, int $numero){
        $conn = ConnectionFactory::getConnection();
        $sql = "SELECT * FROM episode WHERE serie_id = ? AND numero = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id,$numero]);
        $data = $stmt->fetch();
        if ($data === false) throw new EpisodeNotFoundException("Episode $numero de la série $id non trouvé");
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