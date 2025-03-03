<?php

namespace App\Console\Commands\Admin;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Product;
use Illuminate\Console\Command;

class ModelCommand extends Command
{
    protected $signature = 'model:check';
    protected $description = 'временно. исправление ошибок модели';

    public function handle()
    {

        $ids = Product::whereHas('prod_attributes', function ($query) {
            $query->where('id', 15);
        })->get()->pluck('id')->toArray();


        $products = Product::whereNotIn('id', $ids)->get();
        $this->info($products->count());
        $i = 0;
       // $products = Product::where('id', 524)->get(); // BB550BWG
        //TODO Тест на 1 товаре
        /** @var Product $product */
        foreach ($products as $product) {

            $this->info($product->id . ' ' . $product->code);

        }


        return true;
    }
}
