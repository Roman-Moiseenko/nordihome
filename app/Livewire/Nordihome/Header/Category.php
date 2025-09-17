<?php

namespace App\Livewire\Nordihome\Header;

use App\Modules\Base\Helpers\CacheHelper;
use App\Modules\Shop\Repository\ShopRepository;
use Cache;
use Livewire\Component;

class Category extends Component
{
    public mixed $categories;
    public mixed $tree;
    public string $test = "-";

    public function mount() {
        $repository = app()->make(ShopRepository::class);

        $this->categories = Cache::rememberForever(CacheHelper::MENU_CATEGORIES, function () use ($repository) {
            return $repository->getChildren();
        });

        $this->tree = Cache::rememberForever(CacheHelper::MENU_TREES, function () use ($repository) {
            return $repository->getTree();
        });
    }
    public function update_sub(string $_text)
    {
    //    $this->test = $_text;
    }

    public function render()
    {
        return view('livewire.nordihome.header.category');
    }
}
