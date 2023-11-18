<?php

namespace App\View\Components\Widget;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Numeric extends Component
{
    public string $name;
    public float $minValue;
    public float $maxValue;
    public string $id;
    public bool $checked;
    public string $class;
    public ?float $currentMin;
    public ?float $currentMax;

    public function __construct(string $name,
                                float $minValue, float $maxValue,
                                float $currentMin = null, float $currentMax = null,
                                string $id = '', bool $checked = false, string $class = '')
    {
        $this->name = $name;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
        $this->id = empty($id) ? 'id-' . $name : $id;
        $this->checked = $checked;
        $this->class = $class;
        $this->currentMin = $currentMin;
        $this->currentMax = $currentMax;
    }

    public function render(): View|Closure|string
    {
        return view('components.widget.numeric');
    }
}
