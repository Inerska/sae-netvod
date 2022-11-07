<?php

declare(strict_types=1);

namespace Application\datalayer\factory;

use Application\exception\datalayer\DatabaseConnectionException;
use PDO;
use PDOException;

class ConnectionFactory
{
    public static array $config = [];
    public static ?PDO $context = null;

    public static function setConfig($iniFile): void
    {
        self::$config = parse_ini_file($iniFile);
    }

    /**
     * @throws DatabaseConnectionException
     */
    public static function getConnection(): PDO
    {
        if (is_null(self::$context)) {
            try {
                self::$context = new PDO(
                    self::$config['driver'] . ':host=' . self::$config['host'] . ';dbname=' . self::$config['database'],
                    self::$config['username'],
                    self::$config['password']
                );

                self::$context->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$context->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                return self::$context;
            } catch (PDOException $e) {
                throw new DatabaseConnectionException($e->getMessage(), (int)$e->getCode());
            }
        }
        return self::$context;
    }

    public static function closeConnection(): void
    {
        self::$context = null;
    }
}