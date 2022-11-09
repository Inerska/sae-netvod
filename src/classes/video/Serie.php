<?php

namespace Application\video;

use Application\datalayer\factory\ConnectionFactory;
use Application\exception\EpisodeNotFoundException;
use Application\exception\SerieNotFoundException;
use Application\exception\InvalidPropertyNameException;


class Serie {
    protected int $id = 0;
    protected string $titre;
    protected string $descriptif;
    protected array $genre = [];
    protected array $publicVise = [];
    protected string $image;
    protected string $annee;
    protected string $dateAjout;
    protected array $episodes = [] ;
    protected int $nbEpisodes = 0;
    protected mixed $moyenne = 0;
    protected array $commentaires = [];
    protected int $nbCommentaires = 0;

    public function __construct(int $id){
        $conn = ConnectionFactory::getConnection();
        $sql = "SELECT * FROM serie WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        if ($data === false){
            $this->titre = '';
            $this->descriptif = '';
            $this->image = '';
            $this->annee = '';
            $this->dateAjout = '';
        } else {
            //ajout de l'id de la serie
            $this->id = $data['id'];
            //ajout du titre de la serie
            $this->titre = $data['titre'];
            //ajout du descriptif de la serie
            $this->descriptif = $data['descriptif'];
            //ajout de l'image de la serie
            $this->image = $data['img'];
            //ajout de l'année de la serie
            $this->annee = $data['annee'];
            //ajout de la date d'ajout de la serie
            $this->dateAjout = $data['date_ajout'];
            $stmt->closeCursor();

            // Récupération des genres
            $sql = "select libelle from genre inner join serie_genre on serie_genre.idGenre = genre.idGenre INNER JOIN serie on serie.id = serie_genre.idSerie where serie.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            while ($data = $stmt->fetch()){
                $this->genre[] = $data['libelle'];
            }
            $stmt->closeCursor();

            // Récupération des publics visés
            $sql = "select libelle from type inner join serie_type on serie_type.idType = type.idType INNER JOIN serie on serie.id = serie_type.idSerie where serie.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            while ($data = $stmt->fetch()){
                $this->publicVise[] = $data['libelle'];
            }
            $stmt->closeCursor();

            $sql = "select round(sum(note)/count(*), 1) as moyenne from notation where idSerie = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            if ($data) $this->moyenne = $data['moyenne'];
            $stmt->closeCursor();

            $sql = "SELECT email, note, commentaire FROM notation inner join user on notation.idUser = user.id where notation.idSerie = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            while ($data = $stmt->fetch()){
                $this->commentaires[] = $data;
                $this->nbCommentaires++;
            }


            // Récupération des épisodes
            $sql = "SELECT * FROM episode WHERE serie_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            $count = 0;
            while($data = $stmt->fetch()){
                $e = new Episode($data['serie_id'],$data['numero']);
                $this->episodes[] = $e;
                $count++;
            }
            $this->nbEpisodes = $count;
        }
        $stmt->closeCursor();
    }

    public function __get(string $attrname) : mixed
    {
        if (!property_exists($this, $attrname)) throw new InvalidPropertyNameException("Invalid property name : $attrname");
        return $this->$attrname;
    }
}
