<?php

namespace App\View\Components\Company;

use App\Modules\Accounting\Entity\Organization;
use App\Modules\Base\Entity\CompanyModel;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Info extends Component
{
    public Organization $company;
    public string $route = '';
    public string $title;

    public function __construct(
        Organization $company,
        string $route,
        string $title = ''
    )
    {
        $this->company = $company;
        $this->route = $route;
        if (empty($title)) {
            $this->title = $company->short_name;
        } else {
            $this->title = $title;
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.company.info');
    }
}
