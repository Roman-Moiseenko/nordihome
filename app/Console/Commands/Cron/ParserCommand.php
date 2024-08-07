<?php


namespace App\Console\Commands\Cron;


use App\Events\ThrowableHasAppeared;
use App\Jobs\ParserPriceProduct;
use App\Modules\Admin\Entity\Options;
use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\Shop\Parser\ProductParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Deprecated;
use Tests\CreatesApplication;

class ParserCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'cron:parser';
    protected $description = 'Парсим цены товаров';
    protected $app;

    public function handle()
    {
        $settings = new SettingRepository();

        $logger = LoggerCron::new($this->description);


        $this->info('Парсим цены товаров');
        $ikea = Brand::where('name', Brand::IKEA)->first();

        /** @var Product[] $products */
        $products = Product::where('brand_id', $ikea->id)
            ->where('published', true)
            ->where('not_sale', false)
            ->getModels();
        $this->info('Товаров - ' . count($products));

        foreach ($products as $product) {
            if ($this->isIkea($product->code, $product->code_search)) {
                $this->info('Отправлен в очередь - ' . $product->name . ' ' . $product->code);
                ParserPriceProduct::dispatch($logger->id, $product->id, $settings->getParser()->parser_coefficient);
            } else {
                $this->info('*    * Не Икеа ' . $product->name . ' ' . $product->code);
                Log::warning('*    * Не Икеа ' . $product->name . ' ' . $product->code);
            }
        }
    }

    public function isIkea($code, $code_search): bool
    {
        if (strlen($code) != 10) return false;
        if (strlen($code_search) != 8) return false;
        if (!is_numeric($code_search)) return false;
        return true;
    }


    #[Deprecated]
    private function parser_product()
    {
        $logger = LoggerCron::new($this->description);
        $change = false;
        try {
            $this->info('Парсим цены товаров');
            $this->app = $this->createApplication();
            $service = $this->app->make('App\Modules\Shop\Parser\ParserService'); //new ParserService(new HttpPage());
            $products = ProductParser::where('order', true)->get();
            $this->info('Товаров - ' . $products->count());
            /** @var ProductParser $product */
            foreach ($products as $product) {
                $this->info($product->product->name . ' ' . $product->product->code);
                $price = $service->parserCost($product->product->code_search);
                if ($price < 0) {
                    $product->order = false;
                    $product->save(); //Цена не парсится, убираем из продажи
                }
                if ($product->price != $price && $price > 0) {
                    $this->info($product->price . ' != ' . $price);
                    $change = true;
                    $logger->items()->create([
                        'object' => $product->product->name,
                        'action' => 'Изменилась цена (' . $product->price . ')',
                        'value' => price($price),
                    ]);
                    $product->setCost($price);
                }
            }
            if ($change == false) $logger->delete();
        } catch (\Throwable $e) {
            if ($change == false) $logger->delete();
            event(new ThrowableHasAppeared($e));
        }
    }
}
