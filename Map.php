<?php

require_once 'lib/parsecsv.lib.php';

class Map
{
    /**
     * @var array Location map
     */
    private $map = array();

    /**
     * @var string CSV data file name
     */
    private $csv;

    /**
     * @var string CVS data with employees qty and distance between the starting location
     */
    private $csv_employees;

    /**
     * @var int Location quantity
     */
    private $location_qty = 0;

    /**
     * Build location map by CSV file
     *
     * @param string $csv CSV file with location data
     * @param string $csv_employees CSV file with employee information and distance between the starting location
     */
    public function __construct($csv, $csv_employees = null)
    {
        $this->csv = $csv;

        // Build map by CSV
        $this->buildMapByCsv();

        // Employees informations
        if($csv_employees != null) {
            $this->csv_employees = $csv_employees;
            $this->buildEmployees();
        }
    }

    /**
     * Build location map by CSV file
     *
     * @return array
     */
    private function buildMapByCsv()
    {
        // Load data CSV
        $csv_file = new parseCSV();
        $csv_file->auto($this->csv);

        // Loop to set locations
        $index=0; foreach ($csv_file->data as $location_from) {

            if( !empty($location_from[0]) ) {

                $this->map[$index] = array();

                $this->map[$index]['label'] = $location_from[0];

                // Increase location quantity
                $this->location_qty++;

                foreach ($csv_file->data as $location_to) {

                    if( !empty($location_to[0]) ) {

                        $this->map[$index]['to'][] = array(
                            'label'    => utf8_encode($location_to[0])
                        );
                    }
                }

                $index++;
            }
        }

        $distance = array();

        for($i=0; $i < $index; $i++) {

            $x = 0;

            foreach ($csv_file->data[$i] as $value) {

                if($x != 0 ) {

                    if( empty($value) ) {
                        $value = 0;
                    }

                    $distance[$i][] = $value;
                }

                $x++;
            }
        }

        for($i=0; $i < $index; $i++) {

            for($x=0; $x < $index; $x++) {
                $this->map[$i]['to'][$x]['dist'] = $distance[$i][$x];
            }
        }

        return $this->map;
    }

    /**
     * Insert employees informations in the map array
     *
     * @return void
     */
    private function buildEmployees()
    {
        // Load data CSV
        $csv_file = new parseCSV();
        $csv_file->auto($this->csv_employees);

        $index=0; foreach($csv_file->data as $data) {

            if( empty($data['Funcionarios']) ) {
                $data['Funcionarios'] = 0;
            }

            $this->map[$index]['employees']       = $data['Funcionarios'];
            $this->map[$index]['target_distance'] = $data['Distancia'];

            $index++;
        }
    }

    /**
     * Get array map
     *
     * @return array
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Print  loocation map
     */
    public function printMap()
    {
        echo '<pre>';
        print_r($this->map);
        echo '</pre>';
    }

    /**
     * Print resume map
     */
    public function printResumeMap()
    {

        $index=0; foreach($this->map as $location) {
            echo $index.') '.$location['label'].' | Employees: '.$location['employees'].' | Target distance: '.$location['target_distance'].'<br>';
            echo '-------------------------------------------------------------------------------------------- <br>';
            $index++;
        }
    }

    /**
     * Get distance between two locations
     *
     * @param int $location_from Location from
     * @param int $location_to Location to
     * @return float
     */
    public function getDistance($location_from, $location_to)
    {
        return str_replace(",", ".", $this->map[$location_from]['to'][$location_to]['dist']);
    }

    /**
     * Get location label name
     *
     * @param $index Location index
     * @return string
     */
    public function getLocationLabel($index)
    {
        return $this->map[$index]['label'];
    }

    /**
     * Get employees quantity
     *
     * @param int $index Location index
     * @return int
     */
    public function getLocationEmployeesQty($index)
    {
        return $this->map[$index]['employees'];
    }

    /**
     * Get location target distance
     *
     * @param int $index Location index
     * @return float
     */
    public function getLocationTargetDistance($index)
    {
        return str_replace(",", ".", $this->map[$index]['target_distance']);
    }

    /**
     * Get location total quantity
     *
     * @return int
     */
    public function getLocationsQty()
    {
        return $this->location_qty;
    }
}