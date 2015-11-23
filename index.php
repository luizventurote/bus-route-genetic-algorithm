<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bus route Genetic Algorithm</title>
    <link rel="stylesheet" href="style.css">
    <meta charset="utf-8"/>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
</head>

<body>

    <div class="page">

        <h1>Bus route Genetic Algorithm</h1>

        <div class="container">

            <form action="result.php" method="GET">

                <div class="input-text">
                    <label for="pop_size">Population size</label>
                    <input type="text" id="pop_size" name="pop_size" value="500" required>
                </div>

                <div class="input-text">
                    <label for="qty_gen">Quantity generation</label>
                    <input type="text" id="qty_gen" name="qty_gen" value="1000" required>
                </div>

                <div class="input-text">
                    <label for="qty_bus">Quantity bus</label>
                    <input type="text" id="qty_bus" name="qty_bus" value="3" required>
                </div>

                <div class="input-text">
                    <label for="qty_emp">Number of employees on the bus</label>
                    <input type="text" id="qty_emp" name="qty_emp" value="45" required>
                </div>

                <div class="input-text">
                    <label for="max_dist">Maximum distance of bus</label>
                    <input type="text" id="max_dist" name="max_dist" value="48" required>
                </div>

                <input type="submit" class="btn btn-submit" value="Start Algorithm">

            </form>

            <div class="division"></div>

            <div class="copyrith">Develop by <a href="http://luizventurote.com/">Luiz Venturote</a></div>

        </div>

    </div>

</body>
</html>
