<?php

namespace App\Livewire\NBRussia;

use App\Modules\Product\Entity\Category;
use Livewire\Component;

class TableSizes extends Component
{
    public int $category_id;
    public string $data;

    public function mount(int $category_id)
    {
        $this->category_id = $category_id;
        $category = Category::find($category_id);
        $this->data = $this->getDate($category);


    }

    private function getDate(Category $category)
    {
        if (!empty($category->data)) return $category->data;
        if (!is_null($category->parent)) return $this->getDate($category->parent);
        return '';
    }


    public function render()
    {
        return view('livewire.n-b-russia.table-sizes');
    }
}
