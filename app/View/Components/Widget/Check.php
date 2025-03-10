<?php

namespace App\View\Components\Widget;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Check extends Component
{
    public string $name;
    public string $id;
    public bool $checked;
    public string $class;
    public string $route;

    public function __construct(string $name, string $id = '', string $class = '', bool $checked = false, string $route = '')
    {
        $this->name = $name;
        $this->id = empty($id) ? ('id-' . $name) : $id;
        $this->checked = $checked;
        $this->class = $class;
        $this->route = $route;
    }

    public function render(): View|Closure|string
    {
        return view('components.widget.check');
    }
}
