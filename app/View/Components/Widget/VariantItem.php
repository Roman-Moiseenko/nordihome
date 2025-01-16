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
    public mixed $selected = '';
    public bool $checked = false;
    public string $alt;

    public function __construct(string $name, string $id, string $caption, string $image = '', bool $checked = false, string $alt = '')
    {
        /*if (!empty($selected)) {
            if (is_string($selected)) {
                $this->checked = ($id == $selected);
            }
            if (is_array($selected)) {
                $this->checked = in_array($id, $selected);
            }
        }*/
        $this->checked = $checked;
        $this->image_type = !(empty($image));
        $this->name = $name;
        $this->id = $id;
        $this->caption = $caption;
        $this->image = $image;
        $this->alt = $alt;
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
