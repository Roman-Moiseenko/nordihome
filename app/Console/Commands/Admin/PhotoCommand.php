<?php
declare(strict_types=1);

namespace App\Console\Commands\Admin;


use App\Modules\Catalog\Entity\Product;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use function Laravel\Prompts\confirm;

class PhotoCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'photo:patch';
    protected $description = 'Исправление сортировки в фотографиях товаров';

    public function handle(): bool
    {

        if (! $this->confirmToProceed()) {
            return false;
        }

        $this->info('Процесс исправления запущен');
        /** @var Product[] $products */
        $products = Product::withTrashed()->get();
        $this->info('Кол-во товаров - ' . $products->count());
        $_count = 0;
        foreach ($products as $product) {
            $change = false;

            $product->reSort();


            if ($change) {
                $_count++;
                $this->info('   ***** ' . $product->name . ' Изображений ' . $product->photos()->count());
            }

        }
        $this->info('Отсортированы изображения ' . $_count. ' товаров');

        return true;
    }
}
