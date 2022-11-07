<?php

namespace Application\video;

use Application\datalayer\factory\ConnectionFactory;
use Application\exception\EpisodeNotFoundException;


class Serie {
    protected int $id;
    protected string $titre;
    protected string $descriptif;
    protected string $genre;
    protected string $publicVise;
    protected string $image;
    protected string $annee;
    protected string $dateAjout;
    protected array $episodes;
    protected int $nbEpisodes;

    public function __construct(int $id){
        ConnectionFactory::setConfig("config.ini");
        $conn = ConnectionFactory::getConnection();
        $sql = "SELECT * FROM serie WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        $this->id = $data['id'];
        $this->titre = $data['titre'];
        $this->descriptif = $data['descriptif'];
        $this->image = $data['img'];
        $this->annee = $data['annee'];
        $this->dateAjout = $data['date_ajout'];
        $this->genre = '';
        $this->publicVise = '';
        $stmt->closeCursor();
        $sql = "SELECT * FROM episode WHERE serie_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $this->episodes = [];
        $count = 0;
        while($data = $stmt->fetch()){
            $e = new Episode($data['id'],$data['numero']);
            $this->episodes[] = $e;
            $count++;
        }
        $this->nbEpisodes = $count;
        $stmt->closeCursor();
    }

    public function __get(string $attrname) : mixed
    {
        if (!property_exists($this, $attrname)) throw new InvalidPropertyNameException("Invalid property name : $attrname");
        return $this->$attrname;
    }
}
