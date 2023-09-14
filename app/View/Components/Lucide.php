<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Lucide extends Component
{
    public string $icon;
    public string $class;

    /**
     * Create a new component instance.
     */
    public function __construct($icon, $class = '')
    {

        $this->icon = $icon;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.lucide');
    }
}
