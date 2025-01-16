<?php

namespace App\View\Components\Widget;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Variant extends Component
{
    public string $caption;
    public string $id;
    public string $class;

    public function __construct(string $caption, string $id = '', string $class = '')
    {
        $this->caption = $caption;
        $this->id = $id;
        $this->class = $class;
    }

    public function render(): View|Closure|string
    {
        return view('components.widget.variant');
    }
}
