<?php

require_once 'BuildDistrictMap.php';
require_once 'BuildMap.php';

class GeneticAlgorithm
{
    private $district_map;
    private $map;
    private $bus_qty;

    private $population_size = 500;
    private $individuals = array();
    private $dna_size;
    private $gen_count;
    private $gen_max = 500;

    public function __construct(BuildDistrictMap $district_map, BuildMap $map, $bus_qty = 1)
    {
        ini_set('memory_limit', '16M');

        $this->district_map = $district_map;
        $this->map = $map;
        $this->bus_qty = $bus_qty;

        $this->dna_size = 53;

        $this->initPopulation($this->population_size);

        // REMOVE THIS - I USER THIS COE FOR TEST
        $this->startGa();

        foreach($this->individuals as $individual) {
            echo '<pre>';
            print_r($individual['fitness']);
            echo '</pre>';
        }
    }

    public function startGa()
    {
        while ($this->gen_count < $this->gen_max) {
            $this->naturalSelection();
            $this->recreatePopulation();
        }
    }

    public function initPopulation($population_size)
    {

        for ($i = 0; $i < $population_size; $i++) {

            $individual = $this->randomIndividual();

            $this->individuals[$i]['dna'] = $individual;
            $this->individuals[$i]['fitness'] = $this->fitness($individual);
        }

        usort($this->individuals, array("GeneticAlgorithm", "cmpFitness"));


        echo '<pre>';
        //print_r($this->individuals);
        echo '</pre>';
    }

    public function randomIndividual()
    {
        $individual = '';

        for ($i = 0; $i < $this->dna_size; $i++) {
            $individual[] = array(
                'bus' => rand(0, $this->bus_qty),
                'seq' => rand(1, 1000)
            );
        }

        return $individual;
    }

    public function recreatePopulation()
    {
        $this->gen_count++;
        $c = count($this->individuals);
        for ($i=$c; $i<$this->population_size; $i++) {
            $a = rand(0, $c-1);
            $b = rand(0, $c-1);

            $this->crossover($a, $b);

            //array_push($this->individuals, reproduction($POPULATION[$a][0], $POPULATION[$b][0]));
        }
    }

    public function crossover($ia, $ib)
    {
        $indvidual_a = array(
            'dna' => array()
        );

        $indvidual_b = array(
            'dna' => array()
        );

        $crosspoint = rand(0, $this->dna_size-1);

        for($i=0; $i<$crosspoint; $i++) {

            $indvidual_a['dna'][$i] = $this->individuals[$ia]['dna'][$i];
            $indvidual_b['dna'][$i] = $this->individuals[$ib]['dna'][$i];
        }

        for($i=$crosspoint; $i<$this->dna_size; $i++) {

            $indvidual_a['dna'][$i] = $this->individuals[$ib]['dna'][$i];
            $indvidual_b['dna'][$i] = $this->individuals[$ia]['dna'][$i];
        }

        $indvidual_a['fitness'] = $this->fitness($indvidual_a['dna']);
        $indvidual_b['fitness'] = $this->fitness($indvidual_b['dna']);

        $this->individuals[] = $indvidual_a;
        $this->individuals[] = $indvidual_b;
    }

    public function naturalSelection()
    {

        array_splice($this->individuals, ceil($this->population_size/2));





        /*
        $fitness_array = array();

        foreach($this->individuals as $individual) {
            $fitness_array[] = $individual['fitness'] * 10000000;
        }

        echo '<pre>';
        print_r($fitness_array);
        echo '</pre>';
        */
    }

    public function fitness($individual)
    {

        $buses = array();

        for($i = 1; $i <= $this->bus_qty; $i++)
        {
            $buses[$i]['locations'] = array();
            $buses[$i]['employees'] = 0;
        }



        // Employees to bus
        $i =0; foreach($individual as $cell) {

            $bus = $cell['bus'];

            if($bus > 0) {

                // Location label
                $label = $this->map->getLocationLabel($i);

                $employees = $this->district_map->getEmployees($i);

                // Add location in bus
                $buses[$bus]['locations'][] = array(
                    'location' => $i,
                    'location_label' => $label,
                    'employees' => $employees,
                    'order'    => $cell['seq']
                );

                $buses[$bus]['employees'] += $employees;
            }

            $i++;
        }


        for($i = 1; $i <= $this->bus_qty; $i++)
        {
            usort($buses[$i]['locations'], array("GeneticAlgorithm", "cmp"));
        }

        for($i = 1; $i <= $this->bus_qty; $i++)
        {
            $x =0;

            $distance_total = 0;

            foreach($buses[$i]['locations'] as $location) {

                if($x == 0) {
                    $buses[$i]['locations'][$x]['distance'] = 0;
                    $distance_total += 0;
                } else {

                    $from = $buses[$i]['locations'][$x -1]['location'];
                    $to   = $buses[$i]['locations'][$x]['location'];

                    $distance_locations = $this->map->getDistance($from, $to);

                    $buses[$i]['locations'][$x]['distance'] = $distance_locations;

                    $distance_total += $distance_locations;
                }

                $x++;
            }

            $buses[$i]['distance_total'] = $distance_total;
        }

        $distance_total = 0;
        $employees_total = 0;

        for($i = 1; $i <= $this->bus_qty; $i++) {
            $distance_total += $buses[$i]['distance_total'];
            $employees_total += $buses[$i]['employees'];
        }

        $buses['fitness'] = $this->fo($distance_total, $employees_total);

        echo '<pre>';
        //print_r($buses);
        echo '</pre>';

        // Get bus distance


        //var_dump($bus_employees_qty);

        //echo $this->district_map->getEmployees(1);
        //$individual['dna']
        return $buses['fitness'];
    }

    public function cmp($a, $b)
    {
        if ($a['order'] == $b['order']) {
            return 0;
        }
        return ($a['order'] < $b['order']) ? 1 : -1;
    }

    public function cmpFitness($a, $b)
    {
        if ($a['fitness'] == $b['fitness']) {
            return 0;
        }
        return ($a['fitness'] < $b['fitness']) ? 1 : -1;
    }

    public function fo($distance, $employees)
    {

        $result = $employees / $distance;

        if($employees > 48) {
            $result /= 1000;
        }

        return $result;

    }
}