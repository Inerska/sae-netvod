<?php

namespace Application\datalayer\dto;

use Application\datalayer\util\Gender;

class ProfileDto
{
    private int $idProfil;
    private ?string $nom;
    private ?string $prenom;
    private int $age;
    private ?string $gender;
    private ?string $genrePrefere;

    public function __construct(int     $idProfile, ?string $nom,
                                ?string $prenom, int $age, ?string $gender, ?string $genrePref)
    {
        $this->idProfil = $idProfile;
        $this->nom = $nom ?? "Unknown";
        $this->prenom = $prenom ?? "Unknown";
        $this->age = $age;
        $this->gender = $gender ?? "unknown";
        $this->genrePrefere = $genrePref ?? "Unknown";
    }

    /**
     * @return int
     */
    public function getIdProfil(): int
    {
        return $this->idProfil;
    }

    /**
     * @return string|null
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @return string|null
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    /**
     * @param int $idProfil
     */
    public function setIdProfil(int $idProfil): void
    {
        $this->idProfil = $idProfil;
    }

    /**
     * @param string|null $nom
     */
    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @param string|null $prenom
     */
    public function setPrenom(?string $prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @param int $age
     */
    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    /**
     * @param string|null $gender
     */
    public function setGender(?string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @param string|null $genrePrefere
     */
    public function setGenrePrefere(?string $genrePrefere): void
    {
        $this->genrePrefere = $genrePrefere;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @return Gender
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @return string|null
     */
    public function getGenrePrefere(): ?string
    {
        return $this->genrePrefere;
    }

    public function __toString(): string
    {
        return "ProfileDto{" .
            "idProfil=" . $this->idProfil .
            ", nom='" . $this->nom . '\'' .
            ", prenom='" . $this->prenom . '\'' .
            ", age=" . $this->age . '\'' .
            "}";
    }
}