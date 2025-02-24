<?php

namespace App\Console\Commands\Admin;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Product;
use Illuminate\Console\Command;

class SizeCommand extends Command
{
    protected $signature = 'size:check';
    protected $description = 'временно. исправление ошибок';

    public function handle()
    {
        $attribute = Attribute::find(11);


       // $this->info('Атрибут ' . $attribute->name);

        foreach ($attribute->variants as $variant) {
            //$this->info('Проверяем вариант ' . $variant->name);
            $products = Product::whereHas('prod_attributes', function ($query) use ($attribute, $variant) {
                $query->where('attribute_id', $attribute->id)->whereJsonContains('value', $variant->id);
            })->getModels();
            if (count($products) == 1) {

                $this->info('Атрибут ' . $attribute->name . 'Вариант ' .
                    $variant->name . ' id=' . $variant->id . ' Товар ' . $products[0]->code . ' id=' . $products[0]->id);
            } else {
                //$this->info(count($products));
            }
        }


        return true;
    }
}
