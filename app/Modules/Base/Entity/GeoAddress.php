<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

class GeoAddress
{
    public string $address;
    public string $region;
    public string $latitude;
    public string $longitude;
    public string $post;

    public function __construct()
    {
        $this->address = '';
        $this->region = '';
        $this->latitude = '';
        $this->longitude = '';
        $this->post = '';
    }

    public static function create(
        string $address = '',
        string $latitude = '',
        string $longitude = '',
        string $post = '',
        string $region = '',
        array $params = []): self
    {
        $geo = new static();
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                if (isset($geo->$key))
                    $geo->$key = $value ?? '';
            }
        } else {
            $geo->post = $post;
            $geo->address = $address;
            $geo->region = $region;
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
}
