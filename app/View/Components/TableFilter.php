<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableFilter extends Component
{
    public int|null $count;

    public function __construct($count = null)
    {
        $this->count = $count;
    }

    public function render(): View|Closure|string
    {
        return view('components.table-filter');
    }
}
