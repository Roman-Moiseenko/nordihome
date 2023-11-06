<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchProduct extends Component
{
    /**
     * Create a new component instance.
     */
    public string $route;
    public string $inputData;

    public function __construct(string $route, string $inputData)
    {
        $this->route = $route;
        $this->inputData = $inputData;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.search-product');
    }
}
