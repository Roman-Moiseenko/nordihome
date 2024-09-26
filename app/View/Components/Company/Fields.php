<?php

namespace App\View\Components\Company;

use App\Modules\Accounting\Entity\Organization;
use App\Modules\Base\Entity\GeoAddress;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Fields extends Component
{

    public GeoAddress|null $legal_address;
    public GeoAddress|null $actual_address;
    public ?Organization $company;

    public function __construct(Organization|null $company = null)
    {
        $this->company = $company;
        if (is_null($company)) {
            $this->legal_address = new GeoAddress();
            $this->actual_address = new GeoAddress();

        } else {
            $this->legal_address = $company->legal_address;
            $this->actual_address = $company->actual_address;
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.company.fields');
    }
}
