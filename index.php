<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bus route Genetic Algorithm</title>
    <link rel="stylesheet" href="style.css">
    <meta charset="utf-8"/>
</head>

<body>

    <?php

        require_once 'Map.php';
        require_once 'Bus.php';
        require_once 'GeneticAlgorithm.php';

        $map = new Map('data.csv', 'employees.csv');
        $bus = new Bus();

        // Build genetic algorithm
        $ga = new GeneticAlgorithm($map, $bus);

        // Starting genetic algorithm
        $ga->startGa();

        // Best individual
        $individual = $ga->getIndividual(0);

    ?>

    <div class="page">

        <h1>Bus route Genetic Algorithm</h1>

        <div class="container">

            <div><b>Generation:</b> <?php echo $ga->getcountGeneration() ?></div>

            <div class="division"></div>

            <div><b>Fitness:</b> <?php echo $individual['fitness'] ?></div>

            <div class="division"></div>

            <div><b>Employees:</b> <?php echo $individual['employees']['qty'] ?></div>

            <?php for($i=1; $i<=$bus->getBusQty(); $i++): ?>
                <div class="sub-item"><b>Bus <?php echo $i ?>:</b> <?php echo $individual['employees']['bus'][$i] ?></div>
            <?php endfor;?>

            <div class="division"></div>

            <div><b>Distance:</b> <?php echo $individual['distance']['qty'] ?></div>

            <?php for($i=1; $i<=$bus->getBusQty(); $i++): ?>
                <div class="sub-item"><b>Bus <?php echo $i ?>:</b> <?php echo $individual['distance']['bus'][$i] ?></div>
            <?php endfor;?>

            <div class="division"></div>

            <?php for($i=1; $i<=$bus->getBusQty(); $i++): ?>

                <div><b>Bus <?php echo $i ?> route:</b></div>

                <div class="sub-item">Starting location</div>

                <?php foreach($individual['buses'][$i] as $location): ?>
                    <div class="sub-item"><b><?php echo $location['label'] ?></b> (Num.: <?php echo $location['location'] ?> / Employees: <?php echo $location['employees'] ?> / Seq.: <?php echo $location['seq'] ?>)</div>
                <?php endforeach; ?>

                <div class="sub-item">Returns to the starting location</div>

                <div class="division"></div>

            <?php endfor;?>

            <div class="copyrith">Develop by <a href="http://luizventurote.com/">Luiz Venturote</a></div>

        </div>

    </div>

</body>
</html>
