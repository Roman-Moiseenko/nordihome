<?php

namespace App\View\Components\Company;

use App\Modules\Accounting\Entity\Organization;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FieldContact extends Component
{
    public ?Organization $company;

    public function __construct($company)
    {
        $this->company = $company;
    }

    public function render(): View|Closure|string
    {
        return view('components.company.field-contact');
    }
}
