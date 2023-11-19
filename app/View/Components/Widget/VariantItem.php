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
    public string $image;
    private bool $image_type;

    public function __construct(string $name, string $id, string $caption, string $image = '')
    {
        $this->image_type = !(empty($image));
        $this->name = $name;
        $this->id = $id;
        $this->caption = $caption;
        $this->image = $image;
    }

    public function render(): View|Closure|string
    {
        if ($this->image_type) {
            return view('components.widget.variant-item-image');
        } else {
            return view('components.widget.variant-item');
        }
    }
}
