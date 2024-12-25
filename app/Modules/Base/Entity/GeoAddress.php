<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

class GeoAddress
{
    public string $address; //Одной строкой
    public string $post; //Индекс
    public string $region; //Регион
    public string $district; //Район
    public string $city; //*
    public string $street; //*
    public string $house; //*
    public string $room; //*
    public string $latitude;
    public string $longitude;

    public function __construct()
    {
        $this->address = '';
        $this->post = '';
        $this->region = '';
        $this->district = '';
        $this->city = '';
        $this->street = '';
        $this->house = '';
        $this->room = '';
        $this->latitude = '';
        $this->longitude = '';
    }

    public static function create(
        string $address = '',
        string $latitude = '',
        string $longitude = '',
        string $post = '',
        string $region = '',
        string $district ='',
        string $city = '',
        string $street = '',
        string $house = '',
        string $room = '',
        array $params = []): self
    {
        $geo = new static();
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                if (isset($geo->$key))
                    $geo->$key = $value ?? '';
            }
        } else {
            $geo->address = $address;
            $geo->post = $post;
            $geo->region = $region;
            $geo->district = $district;
            $geo->city = $city;
            $geo->street = $street;
            $geo->house = $house;
            $geo->room = $room;
            $geo->latitude = $latitude;
            $geo->longitude = $longitude;
        }
        return $geo;
    }

    public static function fromArray(array|null $params): self
    {
        $geo = new static();
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $geo->$key = $value ?? '';
            }
        }
        return $geo;
    }

    public function toArray(): array
    {
        return json_decode(json_encode($this), true);
        /*
        return [
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'post' => $this->post,
        ]; */
    }

    public function address(bool $index = false): string
    {

        if (!empty($this->address)) {
            $address = $this->address;
        } else {

            $address = implode(', ', [
                $this->region,
                $this->district,
                $this->city,
                $this->street,
                $this->house,
                $this->room
            ]);
        }
        if ($index) $address = $this->post . ', ' . $address;
        return $address;
    }
}
