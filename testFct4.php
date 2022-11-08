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

require_once 'vendor/autoload.php';
$s = new \Application\video\Serie(4);
$sr = new \Application\render\SerieRenderer($s);
echo $sr->render();
echo "<p>--------------------------</p>";




?>
</body>
</html>


