<?php


namespace App\Console\Commands\Cron;


use App\Events\ThrowableHasAppeared;
use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Shop\Parser\HttpPage;
use App\Modules\Shop\Parser\ParserService;
use App\Modules\Shop\Parser\ProductParser;
use Illuminate\Console\Command;

class ParserCommand extends Command
{
    protected $signature = 'cron:parser';
    protected $description = 'Парсим цены товаров';

    public function handle()
    {
        $logger = LoggerCron::new($this->description);
        try {
            $this->info('Парсим цены товаров');

            $change = false;
            $service = new ParserService(new HttpPage());
            $products = ProductParser::get();
            $this->info('Товаров - ' . $products->count());
            /** @var ProductParser $product */
            foreach ($products as $product) {
                $price = $service->parserCost($product->product->code_search);
                $this->info($product->price . ' = ' . $price);
                if ($product->price != $price) {
                    $this->info($product->price . ' = ' . $price);

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
            $logger->delete();
            event(new ThrowableHasAppeared($e));
        }
    }
}
