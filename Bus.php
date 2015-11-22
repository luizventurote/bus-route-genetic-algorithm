<?php

class Bus
{
    /**
     * @var array Buses array
     */
    private $buses = array();

    /**
     * @var int Bus quantity
     */
    private $bus_qty = 3;

    /**
     * @var int Max employees inside a bus
     */
    private $employees_max = 40;

    /**
     * @var int Max distance all busses
     */
    private $bus_max_distance = 48;

    public function getBuses()
    {
        return $this->buses;
    }

    public function setBuses($bus_array)
    {
        $this->buses = $bus_array;
    }

    public function getBusQty()
    {
        return $this->bus_qty;
    }

    public function setBusQty($qty)
    {
        $this->bus_qty = $qty;
    }

    public function getMaxEmployees()
    {
        return $this->employees_max;
    }

    public function setMaxEmployees($qty)
    {
        $this->employees_max = $qty;
    }

    public function getMaxDistance()
    {
        return $this->bus_max_distance;
    }
}