<?php
declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ListCodeProducts extends Component
{

    public string $route;
    public string $captionButton;

    public function __construct(string $route, string $captionButton)
    {
        $this->route = $route;
        $this->captionButton = $captionButton;
    }

    public function render(): View|Closure|string
    {
        return view('components.list-code-products');
    }
}
