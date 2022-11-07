<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>NetVOD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<h1>Bienvenue sur NetVOD</h1>

<?php

use Application\datalayer\factory\ConnectionFactory;
use Application\dispatch\Dispatcher;

require_once "src/views/header.php";
require_once 'vendor/autoload.php';


ConnectionFactory::setConfig( 'db.config.ini' );

session_start();

$action = $_GET['action'] ?? "";

$dispatcher = new Dispatcher($action);

$dispatcher->dispatch();

?>
</body>
</html>