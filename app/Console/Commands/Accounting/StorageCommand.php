<?php

namespace App\Console\Commands\Accounting;

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
            if ($new_count != $product->getCountSell()) {
                $this->info('Изменено кол-во для товара ' . $product->name . ' ' . $product->getCountSell() . ' => ' . $new_count);
                $product->setCountSell($new_count);
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
