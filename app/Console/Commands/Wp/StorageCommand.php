<?php
declare(strict_types=1);

namespace App\Console\Commands\Wp;

use App\Entity\Photo;
use App\Jobs\LoadingImageProduct;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\PricingProduct;
use App\Modules\Accounting\Service\ArrivalService;
use App\Modules\Accounting\Service\PricingService;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use Illuminate\Console\Command;


use Illuminate\Console\ConfirmableTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\CreatesApplication;
use function public_path;

class StorageCommand extends Command
{

    use ConfirmableTrait, CreatesApplication;

    protected $app;
    private Options $options;

    private StorageService $storageService;
    private ArrivalService $arrivalService;

    protected $signature = 'wp:storage
    {--count= : "Кол-во" / "пусто"}';
    protected $description = 'Загрузка товара в хранилище';


    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $this->app = $this->createApplication();
        $this->storageService = $this->app->make('App\Modules\Accounting\Service\StorageService');
        $this->arrivalService = $this->app->make('App\Modules\Accounting\Service\ArrivalService');

        $this->info('Старт');

        $count = $this->option('count');
        $this->options = new Options(); //Настройки Магазина для товара

        $products = Product::where('published', true)->get();

        if (is_numeric($count)) {
            $distributor = Distributor::where('name', 'Икеа Польша')->first();
            //$_id = is_null($distributor) ? 2 : $distributor->id;
            $arrival = $this->arrivalService->create($distributor->id, false);
            $this->info('Создали поступление');

            foreach ($products as $product) {
                $this->arrivalService->add($arrival, ['product_id' => $product->id, 'quantity' => $count]);
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
