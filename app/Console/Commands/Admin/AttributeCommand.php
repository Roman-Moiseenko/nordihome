<?php

namespace App\Console\Commands\Admin;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Product;
use Illuminate\Console\Command;

class AttributeCommand extends Command
{
    protected $signature = 'attribute:clear';
    protected $description = 'Удаление не используемых вариантов атрибутов';

    public function handle()
    {
        $attributes = Attribute::where('type', Attribute::TYPE_VARIANT)->get();

        foreach ($attributes as $attribute) {
            ///$this->info('Атрибут ' . $attribute->name);

            foreach ($attribute->variants as $variant) {
                //$this->info('Проверяем вариант ' . $variant->name);
                $products = Product::whereHas('prod_attributes', function ($query) use ($attribute, $variant) {
                    $query->where('attribute_id', $attribute->id)->whereJsonContains('value', $variant->id);
                })->get()->count();
                if ($products == 0) {

                    $this->info('Атрибут ' . $attribute->name . 'Вариант ' . $variant->name . ' Удален');
                    $variant->delete();
                } else {
                    //$this->info('Используется в ' . $products . ' товарах');
                }
            }


        }
        return true;
    }
}
