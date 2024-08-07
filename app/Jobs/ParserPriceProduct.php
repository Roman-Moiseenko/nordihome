<?php

namespace App\Jobs;

use App\Events\ParserPriceHasChange;
use App\Events\ProductHasBlocked;
use App\Events\ThrowableHasAppeared;
use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Parser\ParserService;
use App\Modules\Shop\Parser\ProductParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Tests\CreatesApplication;

class ParserPriceProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private float $coeff;
    private int $logger_id;
    private int $product_id;

    public function __construct(int $logger_id, int $product_id, float $coeff)
    {
        $this->coeff = $coeff;
        $this->logger_id = $logger_id;
        $this->product_id = $product_id;
    }

    public function handle(ParserService $service): void
    {
        /** @var Product $product */
        $product = Product::find($this->product_id);
        $logger = LoggerCron::find($this->logger_id);
        $coeff = $this->coeff;


        $parser_product = ProductParser::where('product_id', $product->id)->first();
        if (is_null($parser_product)) {
            try {
                $parser_product_data = $service->parsingData($product->code_search);
                $parser_product = $service->createProductParsing($product->id, $parser_product_data);
            } catch (\DomainException $e) {
                Log::info($e->getMessage() . ' ' . $product->code);
                return;
            } catch (\Throwable $e) {
                event(new ThrowableHasAppeared($e));
            }
        }

        $price = $service->parserCost($product->code_search); //В Злотах
        if ($price < 0) {//Цена не парсится, убираем из продажи
            $parser_product->block();
            $product->setDraft();
            event(new ProductHasBlocked($product));
        } else {
            //Проверка цены с таблицы Парсера
            if (!$parser_product->priceEquivalent($price)) { //Цена изменилась, уведомляем менеджера
                $parser_product->setCost($price);
                event(new ParserPriceHasChange($parser_product));
            }
            //Проверка цены по предзаказу
            if ($product->getPricePre() != ceil($price * $coeff)) {
                //Устанавливаем цену для предзаказа
                $product->pricesPre()->create(['value' => ceil($price * $coeff), 'founded' => 'Парсинг - ' . now()]);
                $logger->items()->create([
                    'object' => $product->name,
                    'action' => 'Изменилась цена (' . $product->$price . ')',
                    'value' => price($price),
                ]);
            }
        }
    }

}
