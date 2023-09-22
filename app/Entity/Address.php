<?php


namespace App\Entity;


class Address
{
    public int $index;
    public Region $region; //

    public string $city;
    public string $street;
    public string $house;
    public string $build;
    public string $flat;

    public function __construct($code = Region::DEFAULT_REGION)
    {
        $this->region = Region::getByCode($code);
    }

    public function setRegion($code)
    {
        $this->region = Region::getByCode($code);
    }

    public function fullAddress(): string
    {
        return
            $this->index . ', ' .
            $this->region->name . ', ' .
            $this->city . ', ' .
            $this->street . ', ' .
            'дом ' . $this->house . ', ' .
            (!empty($this->build) ? 'строение ' . $this->build : '') . ', ' .
            (!empty($this->flat) ? 'кв./офис ' . $this->flat : '');
    }

    public function shortAddress(): string
    {
        return
            $this->region->name . ', ' .
            $this->city . ', ' .
            $this->street . ', ' .
            'дом ' . $this->house . ', ' .
            (!empty($this->build) ? 'строение ' . $this->build : '') . ', ' .
            (!empty($this->flat) ? 'кв./офис ' . $this->flat : '');
    }
}
