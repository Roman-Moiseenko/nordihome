<?php

namespace App\Console\Commands\NB;

use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use Illuminate\Console\Command;

class MeasureCommand extends Command
{
    protected $signature = 'measure:check';
    protected $description = 'Установка значения пара для товаров из категорий обуви';

    public function handle()
    {
        $categories_id = [
            713, 808, 877, 910, 918, 931, 935, 937, 943
        ];
        $categories = Category::whereIn('id', $categories_id)->get();

        foreach ($categories as $category) {
            $this->info('Категория ' . $category->name, ' ID = ' . $category->id);

            $products = Product::whereHas('category', function ($query) use ($category) {
                $query->where('_lft', '>=', $category->_lft)->where('_rgt', '<=', $category->_rgt);
            })->where('measuring_id', '<>', 30)->get();
            $this->info('Товаров ' . $products->count());
            /** @var Product $product */
            foreach ($products as $product) {
                $this->info($product->name);
                $product->measuring_id = 30;
                $product->save();
            }

        }


        return true;
    }
}
