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
        $change = false;
        try {
            $this->info('Парсим цены товаров');

            $service = new ParserService(new HttpPage());
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
