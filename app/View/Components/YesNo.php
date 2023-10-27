<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class YesNo extends Component
{
    public string $lucide = 'check';
    public string $title = '';
    public bool $status;
    public string $class = '';

    /**
     * Create a new component instance.
     */
    public function __construct($status, $title = '', $class = '', $lucide = 'check')
    {
        $this->status = $status;
        $this->title = $title;
        $this->class = $class;
        $this->lucide = $lucide;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.yes-no');
    }
}
