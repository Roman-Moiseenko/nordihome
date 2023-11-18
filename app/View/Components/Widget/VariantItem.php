<?php

namespace App\View\Components\Widget;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class VariantItem extends Component
{
    public string $name;
    public string $id;
    public string $caption;
    public string $img;

    /**
     * Create a new component instance.
     */
    public function __construct(string $name, string $id, string $caption, string $img)
    {
        //
        $this->name = $name;
        $this->id = $id;
        $this->caption = $caption;
        $this->img = $img;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.widget.variant-item');
    }
}
