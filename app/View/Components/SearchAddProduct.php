<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchAddProduct extends Component
{
    public string $route; //Ссылка на добавление товара в документ. Метод POST.
    public string $event; //Событие на добавление товара в документ. Через компонент Livewire
    public bool $quantity; //Поле quantity
    public bool $parser; //request-параметр на парсинг товара

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
        if (!empty($routeSave) && !empty($event)) {
            throw new \DomainException('Заполнен маршрут и событие! Должен быть только один');
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
