<?php


namespace App\Console\Commands\Test;


use App\Events\ThrowableHasAppeared;
use App\Jobs\ParserPriceProduct;
use App\Jobs\ParserProduct;
use App\Modules\Admin\Entity\Options;
use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\Shop\Parser\ParserService;
use App\Modules\Shop\Parser\ProductParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Deprecated;
use Tests\CreatesApplication;

class ParserOneCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'parser {code}';
    protected $description = 'Парсим товар по артикулу';

    public function handle(ParserService $service)
    {
        $code = $this->argument('code');

        $product = $service->findProduct($code);
        $this->info($product->name);
        //dd($product->packages);

    }

}
