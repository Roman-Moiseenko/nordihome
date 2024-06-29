<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchAddParser extends Component
{

    public string $route; //Ссылка на добавление товара в документ. Метод POST.
    public string $event; //Событие на добавление товара в документ. Через компонент Livewire
    public bool $quantity; //Поле quantity
    public int $width; //Класс ширины элемента поиска по умолчанию 72: w-72
    public string $caption;

    /**
     * Create a new component instance.
     */
    public function __construct(string $routeSave = '', string $event = '', bool   $quantity = false, int $width = 72, string $caption = 'Добавить товар в документ')
    {

        if (empty($routeSave) && empty($event)) {
            throw new \DomainException('Не заполнен маршрут или событие');
        }
        if (!empty($routeSave) && !empty($event)) {
            throw new \DomainException('Заполнен маршрут и событие! Должен быть только один');
        }
        $this->route = $routeSave;
        $this->event = $event;
        $this->quantity = $quantity;
        $this->caption = $caption;

        $this->width = $width;

        //$this->routeSearch = route('admin.product.search-add');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.search-add-parser');
    }
}
