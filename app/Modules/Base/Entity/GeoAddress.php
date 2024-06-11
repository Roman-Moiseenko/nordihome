<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

class GeoAddress
{
    public string $address;
    public string $latitude;
    public string $longitude;
    public string $post;

    public function __construct()
    {
        $this->address = '';
        $this->latitude = '';
        $this->longitude = '';
        $this->post = '';
    }

    public static function create(string $address, string $latitude, string $longitude, string $post): self
    {
        $geo = new static();
        $geo->post = $post;
        $geo->address = $address;
        $geo->latitude = $latitude;
        $geo->longitude = $longitude;
        return $geo;
    }

    public static function fromArray(?array $params): self
    {
        $geo = new static();
        if (!empty($params)) {
            $geo->address = $params['address'];
            $geo->latitude = $params['latitude'];
            $geo->longitude = $params['longitude'];
            $geo->post = $params['post'];
        }
        return $geo;
    }

    public function toArray(): array
    {
        return [
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'post' => $this->post,
        ];
    }
}
