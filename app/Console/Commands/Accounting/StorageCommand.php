<?php

namespace App\Console\Commands\Accounting;

use App\Entity\Admin;
use App\Modules\Product\Entity\Product;
use Illuminate\Console\Command;

class StorageCommand extends Command
{
    protected $signature = 'storage:recalculate';
    protected $description = 'Пересчет товара, через хранилище';

    public function handle()
    {
        /** @var Product[] $products */
        $products = Product::get();

        foreach ($products as $product) {
            $count_reserves = $product->getReserveCount();
            $count_storages = $this->getStorageCount($product);
            $new_count = $count_storages - $count_reserves;
            if ($new_count != $product->count_for_sell) {
                $this->info('Изменено кол-во для товара ' . $product->name . ' ' . $product->count_for_sell . ' => ' . $new_count);
                $product->count_for_sell = $new_count;
                $product->save();
            }

        }
        return true;
    }

    public function getStorageCount(Product $product): int
    {
        $result = 0;
        foreach ($product->getStorages() as $storage) {
            $result += $storage->getQuantity($product);
        }
        return $result;
    }

}
