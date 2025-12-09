<?php

namespace App\Console\Commands\Admin;

use App\Modules\Discount\Entity\Promotion;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Equivalent;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Series;
use App\Modules\Product\Entity\Tag;
use App\Modules\User\Entity\CartCookie;
use App\Modules\User\Entity\CartStorage;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class ClearShopCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'shop:clear';
    protected $description = 'Удаление товаров, категорий и всех атрибутов';

    public function handle(): bool
    {
        if (!$this->confirmToProceed()) {
            return false;
        }


        //$this->clearItem(Group::get(), 'Группы удалены');
        $this->clearItem(Promotion::get(), 'Акции удалены');
        $this->clearItem(Reserve::get(), 'Резерв очищен');
        $this->clearItem(CartStorage::get(), 'Корзина очищена');
        $this->clearItem(CartCookie::get(), 'Корзина очищена ');
        $this->clearItem(Product::get(), 'Товары удалены', true);
        $this->clearItem(Tag::get(), 'Метки удалены');
        $this->clearItem(Series::get(), 'Серии удалены');
        //$this->clearItem(Equivalent::get(), 'Аналоги удалены');

        foreach (Attribute::get() as $attribute)
            $attribute->categories()->detach();
        $this->clearItem(Attribute::get(), 'Атрибуты удалены');
        $this->clearItem(Category::orderByDesc('_lft')->get(), 'Категории удалены');


        return true;

    }

    private function clearItem($documents, $caption, bool $force = false): void
    {
        foreach ($documents as $item) {
            $item->delete();
            if ($force) $item->forceDelete();
        }
        $this->info($caption);
    }
}
