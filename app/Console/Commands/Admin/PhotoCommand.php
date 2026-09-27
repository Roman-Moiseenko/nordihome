<?php
declare(strict_types=1);

namespace App\Console\Commands\Admin;


use App\Modules\Catalog\Infrastructure\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

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

        //FixMe если нужно, то сделать через PhotoEntity и Job для каждого товара
        $this->info('Отсортированы изображения ' . 0 . ' товаров');

        return true;
    }
}
