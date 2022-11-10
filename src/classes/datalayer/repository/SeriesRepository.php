<?php

namespace Application\datalayer\repository;

use Application\datalayer\factory\ConnectionFactory;
use Application\exception\datalayer\DatabaseConnectionException;

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

    /**
     * @throws DatabaseConnectionException
     */
    final public function getAllSeries(): array
    {
        $db = ConnectionFactory::getConnection();
        $query = $db->query("SELECT * FROM serie");

        $array = [];

        while ($result = $query->fetch()) {
            $array[] = $result;
        }

        return $array;
    }

    final public function getSeriesWith(string $keyword): array
    {
        $db = ConnectionFactory::getConnection();

        $query = $db->prepare("SELECT * FROM serie WHERE titre LIKE :keyword OR descriptif LIKE :keyword");
        $query->execute([
            'keyword' => "%{$keyword}%"
        ]);

        $array = [];

        while ($result = $query->fetch()) {
            $array[] = $result;
        }

        return $array;
    }
}
