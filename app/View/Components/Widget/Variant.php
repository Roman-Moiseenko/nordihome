<?php

namespace App\View\Components\Widget;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Variant extends Component
{

    public string $name;
    public string $id;
    public string $class;

    public function __construct(string $name, string $id = '', string $class = '')
    {

        $this->name = $name;
        $this->id = $id;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.widget.variant');
    }
}
