<?php

namespace Application\datalayer\repository;

use Application\datalayer\dto\ProfileDto;
use Application\datalayer\factory\ConnectionFactory;
use Application\exception\datalayer\DatabaseConnectionException;
use PDO;

class ProfileRepository extends RepositoryBase
{
    private PDO $context;

    /**
     * @throws DatabaseConnectionException
     */
    public function __construct()
    {
        try {
            $this->context = ConnectionFactory::getConnection();
        } catch (DatabaseConnectionException $e) {
            throw new DatabaseConnectionException("Unable to connect to database", 0, $e);
        }
    }

    final public function getProfileById(int $id): ?ProfileDto
    {
        $query = $this->context->prepare("SELECT * FROM profil WHERE idProfil = :id");
        $query->execute(['id' => $id]);

        $result = $query->fetch();

        if ($result === false) {
            return null;
        }

        return new ProfileDto(
            $result["idProfil"],
            $result["nom"],
            $result["prenom"],
            $result["age"],
            $result["genre"],
            $result["genrePrefere"]
        );
    }

    final public function getProfileByUserId(int $userId): ?ProfileDto
    {
        $query = $this->context->prepare("SELECT * FROM profil WHERE idProfil = :id");
        $query->execute(['id' => $userId]);
        $result = $query->fetch();

        if ($result === false) {
            return null;
        }

        return new ProfileDto(
            $result["idProfil"],
            $result["nom"],
            $result["prenom"],
            (int)$result["age"],
            $result["sexe"],
            $result["genrePref"]
        );
    }

    final public function updateProfile(ProfileDto $profile): void
    {
        $query = $this->context->prepare("UPDATE profil SET nom = :nom, prenom = :prenom, age = :age, sexe = :sexe, genrePref = :genrePref WHERE idProfil = :id");
        $query->execute([
            'nom' => $profile->getNom(),
            'prenom' => $profile->getPrenom(),
            'age' => $profile->getAge(),
            'sexe' => $profile->getGender(),
            'genrePref' => $profile->getGenrePrefere(),
            'id' => $profile->getIdProfil()
        ]);
    }
}