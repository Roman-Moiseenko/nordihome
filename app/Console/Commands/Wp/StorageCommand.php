<?php
declare(strict_types=1);

namespace App\Console\Commands\Wp;

use App\Console\CreatesApplication;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Service\ArrivalService;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;


class StorageCommand extends Command
{

    use ConfirmableTrait, CreatesApplication;

    protected $app;
    private StorageService $storageService;
    private ArrivalService $arrivalService;

    protected $signature = 'wp:storage
    {--count= : "Кол-во" / "пусто"}';
    protected $description = 'Загрузка товара в хранилище';


    public function handle()
    {
        if (! $this->confirmToProceed()) return;

        $this->app = $this->createApplication();
        $this->storageService = $this->app->make('App\Modules\Accounting\Service\StorageService');
        $this->arrivalService = $this->app->make('App\Modules\Accounting\Service\ArrivalService');
        $this->info('Старт');
        $count = $this->option('count');
        $products = Product::where('published', true)->get();

        if (is_numeric($count)) {
            $distributor = Distributor::where('name', 'Икеа Польша')->first();
            $arrival = $this->arrivalService->create($distributor->id, false);
            $this->info('Создали поступление');

            foreach ($products as $product) {
                $this->arrivalService->addProduct($arrival, $product->id,  $count);
                $this->info('Товар ' . $product->name . ' Добавлен');
            }
            $this->arrivalService->completed($arrival);
            $this->info('Поступление проведено');

        } else {
            foreach ($products as $product) {
                $this->storageService->add_product($product);
            }
            $this->info('Товары в хранилища загружены');
        }
    }



}
