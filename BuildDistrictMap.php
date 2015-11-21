<?php

require_once 'lib/parsecsv.lib.php';

class BuildDistrictMap
{

    /**
     * @var array district map
     */
    private $district_map = array();

    private $district_qty = 0;

    /**
     * Build location map by CSV file
     *
     * @param string $csv CSV file with location data
     * @return array
     */
    public function buildDistrictMapByCsv($csv)
    {
        // Load data CSV
        $csv_file = new parseCSV();
        $csv_file->auto($csv);

        foreach($csv_file->data as $data) {

            if( empty($data['Funcionarios']) ) {
                $data['Funcionarios'] = 0;
            }

            $this->district_map[] = array(
                'district'  => utf8_encode($data['Bairro']),
                'employees' => $data['Funcionarios'],
                'distance'  => $data['Distancia']
            );

            $this->district_qty++;
        }

        return $this->district_map;
    }

    /**
     * Print  loocation map
     */
    public function printDistrictMap()
    {
        echo '<pre>';
        print_r($this->district_map);
        echo '</pre>';
    }

    public function getArrayLocations()
    {
        $array_location =  array();

        foreach($this->district_map as $location) {
            $array_location[] = $location['district'].'<br>';
        }

        return $array_location;
    }

    public function getDistrictQtd() {
        return $this->district_qty;
    }

    public function getDistrictMapArray()
    {
        return $this->district_map;
    }

    public function getEmployees($index)
    {
        return $this->district_map[$index]['employees'];
    }




}