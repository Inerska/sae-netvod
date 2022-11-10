<?php

use Application\datalayer\factory\ConnectionFactory;
use Application\dispatch\Dispatcher;
use Application\exception\datalayer\DatabaseConnectionException;

session_start();

require_once 'vendor/autoload.php';

ConnectionFactory::setConfig('db.config.ini');

$action = $_GET['action'] ?? '';

$dispatcher = new Dispatcher($action);

try {
    $dispatcher->dispatch();
} catch (DatabaseConnectionException $exception) {
    echo $exception->getMessage();
}

?>