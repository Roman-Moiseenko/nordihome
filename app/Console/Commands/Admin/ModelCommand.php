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

        $products = Product::whereHas('prod_attributes', function ($query) {
            $query->where('id', 21);
        })->where('model', '')->get();

        $this->info($products->count());

        //TODO Тест на 1 товаре
        /** @var Product $product */
        foreach ($products as $product) {
            $value = $product->getProdAttribute(21)->Value();
            $this->info($value);
            $product->model = $value;
            $product->save();
          //  $product->prod_attributes()->detach(21);
        }

        $this->info('*****');
        return true;
    }
}
