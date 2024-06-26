<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchAddProduct extends Component
{
    public string $route;
    public string $event;
    public bool $quantity;
    public bool $parser;

    /**
     * Create a new component instance.
     */
    public function __construct(string $routeSave = '', string $event = '', bool $quantity = false, bool $parser = false)
    {
        //
        $this->route = $routeSave;
        $this->event = $event;
        $this->quantity = $quantity;
        $this->parser = $parser;
        if (empty($routeSave) && empty($event)) {
            throw new \DomainException('Не заполнен маршрут или событие');
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.search-add-product');
    }
}
