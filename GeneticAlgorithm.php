<?php

require_once 'Bus.php';
require_once 'Map.php';

class GeneticAlgorithm
{
    /**
     * @var Map Map with locations informations
     */
    private $map;

    /**
     * @var Bus Buses
     */
    private $bus;

    /**
     * @var int DNA size
     */
    private $dna_size;

    /**
     * @var int Population size
     */
    private $population_size = 500;

    /**
     * @var array Population indiviuals
     */
    private $individuals = array();

    /**
     * @var Generation count
     */
    private $gen_count;

    /**
     * @var int Max generation quantity
     */
    private $gen_max = 1000;

    /**
     * @var bool Enable or disable mutation operator
     */
    private $mutation = true;


    public function __construct(Map $map, Bus $bus)
    {
        // Set new memory limit to execute GA scripts
        ini_set('memory_limit', '128M');

        // Set map
        $this->map = $map;

        // Set bus
        $this->bus = $bus;

        // Set DNA size with location quantity
        $this->dna_size = $this->map->getLocationsQty();
    }

    /**
     * Start Genetic Algorithm script
     */
    public function startGa()
    {
        $this->initPopulation();

        while ($this->gen_count < $this->gen_max) {
            $this->naturalSelection();
            $this->recreatePopulation();
        }
    }

    /**
     * Start initial opulation
     */
    public function initPopulation()
    {
        for($i=0; $i<$this->population_size; $i++) {

            // Get random individual
            $individual = $this->randomIndividual();

            // Get individual fitness
            $individual['fitness'] = $this->fitness($individual);

            // Add individual
            $this->individuals[] = $individual;
        }

        // Sort individuals by fitness
        usort($this->individuals, array("GeneticAlgorithm", "cmpFitness"));
    }

    /**
     * Get radom individual
     *
     * @return array
     */
    public function randomIndividual()
    {
        $individual = array(
            'dna'     => array(),
            'fitness' => null
        );

        for($i=0; $i<$this->dna_size; $i++) {

            // Get location qty employees
            $location_employees = $this->map->getLocationEmployeesQty($i);

            // Location employees
            if($location_employees != 0) {
                $bus = rand(0, $this->bus->getBusQty());
            } else {
                $bus = 0;
            }

            // Target distance
            if($location_employees != 0) {
                $distance_taget = $this->map->getLocationTargetDistance($i);
                $sequence = $distance_taget * 100;
            } else {
                $sequence = rand(1, 1000);
            }

            $individual['dna'][] = array(
                'bus' => $bus,
                'seq' => $sequence
            );
        }

        return $individual;
    }

    /**
     * Recreate population
     */
    public function recreatePopulation()
    {
        // Increase generation
        $this->gen_count++;

        // Recreate population
        $c = count($this->individuals);
        for($i=$c; $i<$this->population_size; $i++) {

            // Select random individuals
            $a = rand(0, $c-1);
            $b = rand(0, $c-1);

            // Crossover
            $this->crossover($a, $b);
        }

        // Sort individuals by fitness
        usort($this->individuals, array("GeneticAlgorithm", "cmpFitness"));
    }

    /**
     * Crossover
     *
     * @param int $ia Individual A
     * @param int $ib Individual B
     */
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

        $indvidual_a['fitness'] = $this->fitness($indvidual_a);
        $indvidual_b['fitness'] = $this->fitness($indvidual_b);

        if($this->mutation) {
            $this->mutation($indvidual_a);
            $this->mutation($indvidual_b);
        }

        $this->individuals[] = $indvidual_a;
        $this->individuals[] = $indvidual_b;
    }

    /**
     * Mutation
     *
     * @param array $indvidual
     */
    public function mutation($indvidual)
    {
        // Get radom individual
        $sample_indvidual = $this->randomIndividual();

        for($i=0; $i<$this->dna_size; $i++) {

            if (rand(0, 100) == 50) {

                // DNA Mutation
                $indvidual['dna'][$i]['bus'] = $sample_indvidual['dna'][$i]['bus'];
            }
        }
    }

    /**
     * Natural Selection
     */
    public function naturalSelection()
    {
        array_splice($this->individuals, ceil($this->population_size/2));
    }

    /**
     * Get individual fitness
     *
     * @param  $individual Individual array
     * @return float
     */
    public function fitness($individual)
    {
        // Start array of the buses
        $buses = array();
        for($i = 1; $i <= $this->bus->getBusQty(); $i++) {
            $buses[$i]['locations'] = array();
            $buses[$i]['employees'] = 0;
        }

        // Employees to bus
        $i=0; foreach($individual['dna'] as $cell) {

            $bus = $cell['bus'];

            if($bus > 0) {

                // Location label
                $label = $this->map->getLocationLabel($i);

                // Employees qty
                $employees = $this->map->getLocationEmployeesQty($i);

                // Add location in bus
                $buses[$bus]['locations'][] = array(
                    'location'       => $i,
                    'location_label' => $label,
                    'employees'      => $employees,
                    'order'          => $cell['seq']
                );

                // Increase employees inside the bus
                $buses[$bus]['employees'] += $employees;
            }

            $i++;
        }

        // Sort bus locations
        for($i=1; $i<=$this->bus->getBusQty(); $i++)
        {
            usort($buses[$i]['locations'], array("GeneticAlgorithm", "cmp"));
        }

        // Set bus distance
        for($i=1; $i<=$this->bus->getBusQty(); $i++) {

            $distance_total = 0;

            $qty_location = count($buses[$i]['locations']);

            $x=0; foreach($buses[$i]['locations'] as $location) {

                if($x == 0) {

                    $buses[$i]['locations'][$x]['distance'] = 0;
                    $distance_total += $this->map->getLocationTargetDistance( $buses[$i]['locations'][$x]['location'] );

                } else {

                    $from = $buses[$i]['locations'][$x -1]['location'];
                    $to   = $buses[$i]['locations'][$x]['location'];

                    // Get distance between locations
                    $distance_locations = $this->map->getDistance($from, $to);

                    // Set distance value
                    $buses[$i]['locations'][$x]['distance'] = $distance_locations;

                    // Increase total distance
                    $distance_total += $distance_locations;

                    // Last location
                    if($x+1 == $qty_location) {
                        $distance_total += $this->map->getLocationTargetDistance( $buses[$i]['locations'][$x]['location'] );
                    }
                }

                $x++;
            }

            // Set bus total distance
            $buses[$i]['distance_total'] = $distance_total;
        }

        $distance_total  = 0;
        $employees_total = 0;
        $buses_employees = array();

        // Join employees and distance of all buses
        for($i=1; $i<=$this->bus->getBusQty(); $i++) {
            $distance_total  += $buses[$i]['distance_total'];
            $employees_total += $buses[$i]['employees'];

            $buses_employees[] = $buses[$i]['employees'];
        }

        // Get individual object function value
        $buses['fitness'] = $this->fo($distance_total, $employees_total, $buses_employees);

        return number_format($buses['fitness'], 8);
    }

    /**
     * Used to sort bus order locations
     *
     * @param $a Item a
     * @param $b Item b
     * @return int
     */
    public function cmp($a, $b)
    {
        if ($a['order'] == $b['order']) {
            return 0;
        }

        return ($a['order'] < $b['order']) ? 1 : -1;
    }

    /**
     * Used to sort item by fitness
     *
     * @param $a Item a
     * @param $b Item b
     * @return int
     */
    public function cmpFitness($a, $b)
    {
        if ($a['fitness'] == $b['fitness']) {
            return 0;
        }
        return ($a['fitness'] < $b['fitness']) ? 1 : -1;
    }

    /**
     * Used to sort item by sequence
     *
     * @param $a Item a
     * @param $b Item b
     * @return int
     */
    public function cmpSequence($a, $b)
    {
        if ($a['seq'] == $b['seq']) {
            return 0;
        }
        return ($a['seq'] < $b['seq']) ? -1 : 1;
    }

    /**
     * Objective function
     *
     * @param  $distance Total distance
     * @param  $employees Total employees
     * @param  array $buses_employees
     * @return float
     */
    public function fo($distance, $employees, $buses_employees = null)
    {

        $result = $employees / $distance;

        $bonus = false;
        $bonus_fail = 0;
        $super_bonus = 0;

        if($distance > $this->bus->getMaxDistance()) {
            $result /= 1000;
            $bonus_fail++;
        } else {

            if( !($distance > ($this->bus->getMaxDistance()*0.7)) ) {
                $bonus_fail++;
            }
        }

        if($buses_employees != null) {

            $qty = count($buses_employees);

            foreach($buses_employees as $emp) {

                if($emp > $this->bus->getMaxEmployees()) {
                    $result /= 1000;
                    $bonus_fail++;
                } else {

                    if( !($emp > ($this->bus->getMaxEmployees()*0.7)) ) {
                        $bonus_fail++;
                    }

                    // Super bonus
                    if( ($emp > ($this->bus->getMaxEmployees()*0.8)) ) {
                        $super_bonus++;
                    }
                }
            }

            if( ($super_bonus == $qty) && ($bonus_fail == 0) ) {
                $result += 5;
            }
        }

        if($bonus_fail == 0) {
            $result += 10;
        }

        return $result;
    }

    /**
     * Get individual informations
     *
     * @param $individual
     * @return array
     */
    public function getIndividualInformation($individual)
    {
        $ind = array(
            'fitness'   => $individual['fitness'],
            'employees' => array(
                'qty' => 0
            ),
            'distance' => array(
                'qty' => 0
            ),
            'buses'     => array()
        );

        $distance_total  = 0;
        $employees_total = 0;

        $i=0; $x=0; foreach($individual['dna'] as $item) {

            $bus = $item['bus'];

            if($bus != 0) {

                $employees       = $this->map->getLocationEmployeesQty($i);

                $ind['buses'][ $bus ][] = array(
                    'location'         => $i,
                    'label'            => $this->map->getLocationLabel($i),
                    'employees'        => $employees,
                    'seq'              => $item['seq']
                );

                $employees_total += $employees;

                $x++;
            }

            $i++;
        }

        // Sort bus locations by sequence
        for($i=1; $i<=$this->bus->getBusQty(); $i++) {
            usort($ind['buses'][$i], array("GeneticAlgorithm", "cmpSequence"));
        }

        // Employees qty
        for($i=1; $i<=$this->bus->getBusQty(); $i++) {

            $bus_location_qty = count($ind['buses'][$i]);

            $ind['employees']['bus'][$i] = 0;

            for($x=0; $x<$bus_location_qty; $x++) {
                $ind['employees']['bus'][$i] += $ind['buses'][$i][$x]['employees'];
                $ind['employees']['qty'] += $ind['buses'][$i][$x]['employees'];
            }
        }

        // Distance qty
        for($i=1; $i<=$this->bus->getBusQty(); $i++) {

            $bus_location_qty = count($ind['buses'][$i]);

            $ind['distance']['bus'][$i] = 0;

            for($x=0; $x<$bus_location_qty; $x++) {

                // First location
                if($x == 0) {

                    $distance = $this->map->getLocationTargetDistance( $ind['buses'][$i][$x]['location'] );
                    $ind['distance']['bus'][$i] += $distance;
                    $ind['distance']['qty'] += $distance;

                } else {

                    $distance = $this->map->getDistance($ind['buses'][$i][$x-1]['location'], $ind['buses'][$i][$x]['location']);
                    $ind['distance']['bus'][$i] += $distance;
                    $ind['distance']['qty'] += $distance;

                    // Last location
                    if($x+1 == $bus_location_qty) {
                        $distance = $this->map->getLocationTargetDistance( $ind['buses'][$i][$x]['location'] );
                        $ind['distance']['bus'][$i] += $distance;
                        $ind['distance']['qty'] += $distance;
                    }
                }
            }
        }

        return $ind;
    }

    /**
     * Get individual by index
     *
     * @param int $index
     * @return array
     */
    public function getIndividual($index)
    {
        return $this->getIndividualInformation( $this->individuals[$index] );
    }

    /**
     * Get cout generation
     *
     * @return int
     */
    public function getcountGeneration()
    {
        return $this->gen_count;
    }

    public function setQtyMaxGeneration($value)
    {
        $this->gen_max = $value;
    }

    public function setPopulationSize($value)
    {
        $this->population_size = $value;
    }
}