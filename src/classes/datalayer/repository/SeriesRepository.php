<?php

namespace Application\datalayer\repository;

use Application\datalayer\factory\ConnectionFactory;

class SeriesRepository extends RepositoryBase
{
    final public function addSeriesToPreferences(int $seriesId, int $userId): void
    {
        $connection = ConnectionFactory::getConnection();
        $query = "INSERT INTO user_serie_pref (idUser, idSerie) VALUES (:user_id, :series_id)";
        $statement = $connection->prepare($query);

        $statement->execute([
            'user_id' => $userId,
            'series_id' => $seriesId
        ]);
    }

    final public function removeSeriesToPreferences(int $seriesId, int $userId): void
    {
        $connection = ConnectionFactory::getConnection();
        $query = "DELETE FROM user_serie_pref WHERE idUser = :user_id AND idSerie = :series_id";
        $statement = $connection->prepare($query);

        $statement->execute([
            'user_id' => $userId,
            'series_id' => $seriesId
        ]);
    }
}