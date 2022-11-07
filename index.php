<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>NetVOD</title>
</head>
<body>
<h1>Bienvenue sur NetVOD</h1>

<?php

use Application\dispatch\Dispatcher;

require_once 'vendor/autoload.php';

echo "Hello World!";

\Application\datalayer\factory\ConnectionFactory::setConfig('db.config.ini');

$dispatch = new Dispatcher($_GET['action'] ?? null);
$dispatch->dispatch();
?>
</body>
</html>