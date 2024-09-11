<?php
declare(strict_types=1);

namespace App\View\Components;

use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use Illuminate\View\Component;

class CreateAddProduct extends Component
{
    public string $route; //Ссылка на добавление товара в документ. Метод POST.
    public string $event; //Событие на добавление товара в документ. Через компонент Livewire
    public string $routeCreate = ''; //Адрес поиска
    public mixed $categories;
    public mixed $brands;

    public function __construct(string $routeSave = '', string $event = ''
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
        $this->routeCreate = route('admin.product.fast-create');

        $this->categories = Category::defaultOrder()->withDepth()->get();
        $this->brands = Brand::orderBy('name')->get();
    }

    public function render()
    {
        return view('components.create-add-product');
    }
}
