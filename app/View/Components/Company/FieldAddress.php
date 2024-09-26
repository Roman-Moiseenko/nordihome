<?php

namespace App\View\Components\Company;

use App\Modules\Base\Entity\GeoAddress;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FieldAddress extends Component
{
    public GeoAddress $legal_address;
    public GeoAddress $actual_address;

    public function __construct(GeoAddress $legalAddress = new GeoAddress(), GeoAddress $actualAddress = new GeoAddress())
    {
        $this->legal_address = $legalAddress;
        $this->actual_address = $actualAddress;
    }

    public function render(): View|Closure|string
    {
        return view('components.company.field-address');
    }
}
