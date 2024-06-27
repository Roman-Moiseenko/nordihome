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
    public bool $published; //Только опубликованные товары (для Заказа)
    public bool $parser; //request-параметр на парсинг товара ??
    //Параметры отображения
    public int $width; //Класс ширины элемента поиска по умолчанию 72: w-72
    public bool $showImage = false; //Показывать изображение в списке !! На будущее !!
    public bool $showStock = false; //Показывать кружок в наличии или нет товар
    public bool $showCount = false; //Показывать кол-во в списке

    public string $routeSearch; //Адрес поиска


    /**
     * Create a new component instance.
     */
    public function __construct(string $routeSave = '', string $event = '', bool $published = false,
                                bool   $quantity = false, bool $parser = false, int $width = 72,
                                bool   $showImage = false, bool $showStock = false, bool $showCount = false
    )
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
        $this->published = $published;
        $this->parser = $parser;

        $this->width = $width;
        $this->showImage = $showImage;
        $this->showStock = $showStock;
        $this->showCount = $showCount;

        $this->routeSearch = route('admin.product.search-add');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.search-add-product');
    }
}
