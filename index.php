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

            <div id="map"></div>

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

                <div class="location-box">

                    <div class="sub-item location-start">Starting location</div>

                    <?php foreach($individual['buses'][$i] as $location): ?>
                        <div class="sub-item"><b class="location"><?php echo $location['label'] ?></b> (Num.: <?php echo $location['location'] ?> / Employees: <?php echo $location['employees'] ?> / Seq.: <?php echo $location['seq'] ?>)</div>
                    <?php endforeach; ?>

                    <div class="sub-item location-end">Returns to the starting location</div>

                    <div class="btn-show-locations">Show Map Location</div>

                    <div class="division"></div>

                </div>

            <?php endfor;?>

            <div class="copyrith">Develop by <a href="http://luizventurote.com/">Luiz Venturote</a></div>

        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCVP49gAwSzPlMeqxRlc-kzYG_vUL2upz4&signed_in=true&callback=initMap&v=3.21" async defer></script>

    <script>

        function initMap() {
            var directionsService = new google.maps.DirectionsService;
            var directionsDisplay = new google.maps.DirectionsRenderer;
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 13,
                center: {lat: -19.5363148, lng: -40.6470767}
            });
            directionsDisplay.setMap(map);

            $('.btn-show-locations').click(function() {

                var locations = [];

                $(this).parent('.location-box').find('.location').each(function() {

                    locations.push( $(this).text()+', Colatina, ES, Brasil' );

                });

                calculateAndDisplayRoute(directionsService, directionsDisplay, locations);

                $("html, body").animate({ scrollTop: 0 }, 500);

            });
        }

        function calculateAndDisplayRoute(directionsService, directionsDisplay, locations) {
            var waypts = [];
            for (var i = 0; i < locations.length; i++) {

                    waypts.push({
                        location: locations[i],
                        stopover: true
                    });
            }

            directionsService.route({
                origin: {lat: -19.5217397, lng: -40.6293425},
                destination: {lat: -19.5217397, lng: -40.6293425},
                waypoints: waypts,
                optimizeWaypoints: true,
                travelMode: google.maps.TravelMode.DRIVING
            }, function(response, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                    var route = response.routes[0];
                    var summaryPanel = document.getElementById('directions-panel');
                    summaryPanel.innerHTML = '';
                    // For each route, display summary information.
                    for (var i = 0; i < route.legs.length; i++) {
                        var routeSegment = i + 1;
                        summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
                            '</b><br>';
                        summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
                        summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
                        summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
                    }
                } else {
                    window.alert('Directions request failed due to ' + status);
                }
            });
        }

    </script>

</body>
</html>
