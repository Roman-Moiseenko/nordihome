<?php

namespace App\Console\Commands\Admin;

use App\Modules\Accounting\Entity\PricingDocument;
use Illuminate\Console\Command;

class PricingCommand extends Command
{
    protected $signature = 'db:pricing {id}';

    protected $description = 'Установка Цен';

    public function handle(): bool
    {
        $id = $this->argument('id');
        /** @var PricingDocument $pricing */
        $pricing = PricingDocument::find($id);
        if (is_null($pricing)) {
            $this->error('Не найден документ');
            return false;
        }
        $this->warn('Найдено ' . $pricing->products()->count() . ' товаров');
        foreach ($pricing->products as $product) {
            $new = $product->price_min * rand(10, 1000) + 99;
            $product->price_retail = $new;
            $product->price_bulk = ceil($new * 0.9);
            $product->price_special = ceil($new * 0.8);
            $product->price_pre = ceil($new * 0.7);
            $product->save();
            $this->info('Для ' . $product->product->name . ' Цена установлена');
        }

        return true;
    }
}
