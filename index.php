<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>

<style>
    
    body {
        background: #333;
        color: #FFF;
    }
    
</style>

<body>


<?php

/*
 * (N�mero de Funcion�rios / Dist�ncia percorrida pelo �nibus) / 1000
 * Fazer Roleta para selecionar indiv�duos de acordo com o seu fitness
 * 3 �nibus
 * A soma dos tr�s n�o pode ultrapassar 48Km
 *
 */

require_once 'BuildMap.php';
require_once 'BuildDistrictMap.php';
require_once 'GeneticAlgorithm.php';

$bus_qty = 3;

$buildMap = new BuildMap();

$buildMap->buildMapByCsv('data.csv');

//$buildMap->printMap();

$buildDistrictMap = new BuildDistrictMap();

$buildDistrictMap->buildDistrictMapByCsv('staff.csv');

//$buildDistrictMap->printDistrictMap();

//echo $buildDistrictMap->getDistrictQtd();

// Build genetic algorithm
$ga = new GeneticAlgorithm($buildDistrictMap, $buildMap, $bus_qty);

?>

</body>
</html>
