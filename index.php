<!doctype html>
<html lang="fr" class="bg-white dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>NetVOD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<?php

use Application\datalayer\factory\ConnectionFactory;
use Application\dispatch\Dispatcher;
use Application\exception\datalayer\DatabaseConnectionException;

session_start();

require_once 'vendor/autoload.php';

ConnectionFactory::setConfig( 'db.config.ini' );

$action = $_GET['action'] ?? "";

$dispatcher = new Dispatcher($action);

try {
    $dispatcher->dispatch();
} catch (DatabaseConnectionException $e) {
    echo $e->getMessage();
}

?>
</body>
</html>