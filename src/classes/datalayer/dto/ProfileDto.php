<?php

namespace Application\datalayer\dto;

use Application\datalayer\util\Gender;

class ProfileDto
{
    public int $idProfil = -1;
    public ?string $nom = null;
    public ?string $prenom = null;
    public int $age = -1;
    public Gender $gender = Gender::Other;
    public ?string $genrePrefere = null;
}