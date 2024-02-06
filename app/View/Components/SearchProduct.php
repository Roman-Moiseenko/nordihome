<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchProduct extends Component
{
    /**
     * Create a new component instance.
     */
    public string $route; //адрес ajax-post запроса с возвратом списка товаров
    public string $inputData; //id input со всеми данными, value = название товара
    public string $hiddenId; //доп. скрытый input с value = id выбранного товара в id="hidden-id" name="$hiddenId"
    public mixed $callback;//ф-ция обратного вызова(), срабатывает при выборе элемента из списка
    public string $class;

    public function __construct(string $route, string $inputData, string $hiddenId = '', string $callback = null, string $class = '')
    {
        $this->route = $route;
        $this->inputData = $inputData;
        $this->hiddenId = $hiddenId;
        $this->callback = $callback;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.search-product');
    }
}
