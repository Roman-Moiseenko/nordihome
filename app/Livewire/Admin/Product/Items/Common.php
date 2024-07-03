<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Series;
use App\Modules\Product\Entity\Tag;
use App\Modules\Product\Helper\ProductHelper;
use App\Modules\Product\Service\SeriesService;
use Illuminate\Support\Str;
use Livewire\Component;

class Common extends Component
{

    public Product $product;

    public string $name;
    public string $code;
    public string $slug;
    public int $category_id;
    public array $_categories;
    public int $brand_id;
    public mixed $series_id;

    public mixed $categories;
    public mixed $brands;
    public mixed $series;

    public array $errors;

    public function mount(Product $product)
    {
        $this->product = $product;

        $this->categories = Category::defaultOrder()->withDepth()->get();
        $this->brands = Brand::orderBy('name')->get();
        $this->series = Series::orderBy('name')->get();

        $this->refresh_fields();
    }

    public function refresh_fields()
    {
        $this->name = $this->product->name;
        $this->code = $this->product->code;
        $this->slug = $this->product->slug;
        $this->category_id = $this->product->main_category_id;
        $this->brand_id = $this->product->brand_id;
        $this->series_id = $this->product->series_id;

        $this->_categories = array_map(function (Category $category) {
            return $category->id;
        }, $this->product->categories()->getModels());
    }

    private function error($field, $message)
    {
        $this->errors[$field] = true;
        throw new \DomainException($message);
    }

    public function updating($property, $value)
    {
        $this->errors = [];

        //Проверка на уникальное имя
        if ($property == 'name') {
            if (empty($value)) $this->error('name', 'Название товара обязательное поле');
            $find = Product::where('name', $value)->first();
            if (!is_null($find) && $find->id != $this->product->id)
                $this->error('name', 'Товар с таким названием уже существует');
            unset($this->errors['name']);
        }

        //Проверка на уникальный slug
        if ($property == 'slug' && $value != '') {
            $find = Product::where('slug', $value)->first();
            if (!is_null($find) && $find->id != $this->product->id)
                $this->error('slug', 'Товар с такой ссылкой уже существует');
            unset($this->errors['slug']);
        }
        //Проверка на уникальный артикул
        if ($property == 'code') {
            if (empty($value))
                $this->error('code', 'Артикул товара обязательное поле');
            $find = Product::where('code', $value)->first();
            if (!is_null($find) && $find->id != $this->product->id)
                $this->error('code', 'Товар с таким артикулом уже существует');

            unset($this->errors['code']);
        }
    }

    public function save() //Сохраняем текстовые поля
    {
        if ($this->slug == '') $this->slug = Str::slug($this->name);

        $this->product->name = $this->name;
        $this->product->slug = $this->slug;
        $this->product->code = $this->code;
        $this->product->save();

        $this->dispatch('product-update-name');
    }

    //Сохраняем гл.категорию
    public function change_category()
    {
        //TODO Проверка на модификации ???

        //$old_attr = array_map(function ($attribute) {return $attribute->id;}, $this->product->category->all_attributes());
       // $new_attr = array_map(function ($attribute) {return $attribute->id;}, Category::find($this->category_id)->all_attributes());

        /*foreach ($old_attr as $i => $id) {
            if (in_array($id, $new_attr)) unset($old_attr[$i]);
        } */


/*
        foreach ($old_attr as $id) {
            if ($this->product->AttributeIsModification($id)) {
                $this->refresh_fields();
                $this->dispatch(
                    'window-notify',
                    title: 'Ошибка',
                    message: 'При смене категории недоступен атрибут который является ключевым в модификации товара');
                $this->dispatch(
                    'tom-select-sync',
                    id: ('select-category-component'),
                    value: $this->product->main_category_id);
                return;
            }
        }*/

        if (!$this->check_modification(true)) return;

        $this->product->main_category_id = $this->category_id;
        $this->product->save();
        $this->dispatch('change-category');
    }

    //Сохраняем доп.категории
    public function change_categories()
    {

        if (!$this->check_modification(false)) return;

        //Проверить изменения в списке категорий
        $array_old = [];
        $array_new = $this->_categories;



        foreach ($this->product->categories as $category) $array_old[] = $category->id;
        foreach ($array_old as $key => $item) {
            if (!empty($array_new) && in_array($item, $array_new)) {
                $key_new = array_search($item, $array_new);
                unset($array_old[$key]);
                unset($array_new[$key_new]);
            }
        }
        foreach ($array_old as $item) {
            $this->product->categories()->detach((int)$item);
        }
        if (!empty($array_new)) {
            foreach ($array_new as $item) {
                if (!is_null(Category::find((int)$item))) {
                    $this->product->categories()->attach((int)$item);
                } else {
                    $this->dispatch('window-notify', title: 'Ошибка', message: 'Категория не найдена');
                }
            }
        }

        $this->refresh_fields();

        $this->dispatch('change-category');
    }

    public function change_brand() //Сохраняем Бренд
    {
        $this->product->brand_id = $this->brand_id;
        $this->product->save();
    }

    public function change_series() //Сохраняем Серию
    {
        $seriesService = new SeriesService();
        if (empty($this->series_id)) {
            $this->product->series_id = null;
            $this->save();
            return;
        }
        if (is_array($this->series_id)) {
            $this->series_id = array_shift($this->series_id);
        }
        if (is_numeric($this->series_id)) {
            $this->product->series_id = (int)$this->series_id;
        } else {
            $series = $seriesService->registerName($this->series_id); //Создаем Серию
            $this->product->series_id = $series->id;
        }
        $this->save();
    }

    public function render()
    {
        return view('livewire.admin.product.items.common');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }

    private function check_modification(bool $main_category): bool
    {
        //Собираем все атрибуты для старых значений категорий
        $old_attr = array_map(function ($attribute) {return $attribute->id;}, $this->product->category->all_attributes());
        foreach ($this->product->categories as $category) {
            $old_attr = array_unique(array_merge(
                $old_attr,
                array_map(function ($attribute) {return $attribute->id;}, Category::find($category->id)->all_attributes())
            ));
        }

        //Собираем все атрибуты для новых значений категорий
        $new_attr = array_map(function ($attribute) {return $attribute->id;}, Category::find($this->category_id)->all_attributes());
        foreach ($this->_categories as $_cat_id) {
            $new_attr = array_unique(array_merge(
                $new_attr,
                array_map(function ($attribute) {return $attribute->id;}, Category::find($_cat_id)->all_attributes())
            ));
        }



        foreach ($old_attr as $i => $id) {
            if (in_array($id, $new_attr)) unset($old_attr[$i]);
        }

        foreach ($old_attr as $id) {
            if ($this->product->AttributeIsModification($id)) {
                $this->refresh_fields();
                $this->dispatch(
                    'window-notify',
                    title: 'Ошибка',
                    message: 'При смене категории недоступен атрибут который является ключевым в модификации товара');

                if ($main_category) {
                    $this->dispatch('tom-select-sync', id: 'select-category-component', value: $this->product->main_category_id);
                } else {

                    $_old_cats = array_map(function (Category $category) {
                        return $category->id;
                    }, $this->product->categories()->getModels());

                    $this->dispatch('tom-select-sync', id: 'select-categories-component', value: json_encode($_old_cats));

                }

                return false;
            }
        }
        return true;
    }

}
