<?php

namespace App\Modules\Auth\Domain\ValueObjects;

use InvalidArgumentException;

final class Address
{
    public ?string $country = null {
        get => $this->country;
    }
    public ?int $regionCode = null {
        get => $this->regionCode;
        set => $this->regionCode = $value;
    }

    public ?string $region = null {
        get => $this->region;
    }
    public ?string $city = null {
        get => $this->city;
    }
    public ?string $street = null {
        get => $this->street;
    }
    public ?string $house = null {
        get => $this->house;
    }
    public ?string $building = null {
        get => $this->building;
    }
    public ?string $flat = null {
        get => $this->flat;
    }
    public ?string $postalCode = null {
        get => $this->postalCode;
    }

    public function __construct(
        ?string  $country = null,
        ?string  $city = null,
        ?string  $street = null,
        ?string $region = null,
        ?string $postalCode = null,
        ?int $regionCode = null,
        ?string $house = null,
        ?string $building = null,
        ?string $flat = null,
    )
    {
        $this->country = trim($country);
        $this->city = trim($city);
        $this->street = trim($street);
        $this->region = $region ? trim($region) : null;
        $this->postalCode = $postalCode ? trim($postalCode) : null;
        $this->regionCode = $regionCode;
        $this->house = $house ? trim($house) : null;
        $this->building = $building ? trim($building) : null;
        $this->flat = $flat ? trim($flat) : null;
    }

    public function getFullAddress(): string
    {
        $parts = [
            $this->postalCode,
            $this->country,
            $this->region,
            $this->city,
            $this->street,
            $this->house ? 'д. ' . $this->house : null,
            $this->building ? 'стр. ' . $this->building : null,
            $this->flat ? 'пом. ' . $this->flat : null,
        ];
        return implode(', ', array_filter($parts));
    }

    public function __toString(): string
    {
        return $this->getFullAddress();
    }

    public function equals(self $other): bool
    {
        return $this->country === $other->country
            && $this->region === $other->region
            && $this->city === $other->city
            && $this->street === $other->street
            && $this->house === $other->house
            && $this->building === $other->building
            && $this->flat === $other->flat
            && $this->postalCode === $other->postalCode;
    }
}
