<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Modules\Base\Entity\GeoAddress as GeoAddressEntity;

class GeoAddress extends Component
{
    public GeoAddressEntity $address;
    public string $field;
    public string $title;
    public bool $map;

    public function __construct(
        GeoAddressEntity|null $address,
        string $title,
        string $field = 'address',
        bool $map = false
    )
    {
        if (is_null($address)) $address = new GeoAddressEntity();
        $this->address = $address;
        $this->field = $field;
        $this->title = $title;
        $this->map = $map;

    }


    public function render(): View|Closure|string
    {
        return view('components.geo-address');
    }
}
