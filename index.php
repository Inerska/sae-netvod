<!doctype html>
<html lang="fr" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>NetVOD</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
          integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
            integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="js/app.js"></script>
    <link rel="stylesheet" href="css/tailwind.css">
    <link rel="stylesheet" href="css/dark.css">
</head>
<body class="bg-white dark:bg-gray-800 h-full transition-colors duration-1000 antialiased">
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
</body>
</html>