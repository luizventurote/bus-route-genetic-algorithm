<?php

require_once 'lib/parsecsv.lib.php';

class BuildMap
{
    /**
     * @var array Location map
     */
    private $map = array();

    /**
     * Build location map by CSV file
     *
     * @param string $csv CSV file with location data
     * @return array
     */
    public function buildMapByCsv($csv)
    {
        // Load data CSV
        $csv_file = new parseCSV();
        $csv_file->auto($csv);

        // Loop to set locations
        $index=0; foreach ($csv_file->data as $location_from) {

            if( !empty($location_from[0]) ) {

                $this->map[$index] = array();

                $this->map[$index]['label'] = $location_from[0];

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
     * Print  loocation map
     */
    public function printMap()
    {
        echo '<pre>';
        print_r($this->map);
        echo '</pre>';
    }

    /**
     * @param int $location_from
     * @param int $location_to
     */
    public function getDistance($location_from, $location_to)
    {
        return $this->map[$location_from]['to'][$location_to]['dist'];
    }

    public function getLocationLabel($index)
    {
        return $this->map[$index]['label'];
    }
}