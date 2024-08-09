<?php

namespace App\Jobs;

use App\Events\ParserPriceHasChange;
use App\Events\ProductHasBlocked;
use App\Events\ThrowableHasAppeared;
use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Service\ProductService;
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

    private int $logger_id;
    private int $product_id;

    public function __construct(int $logger_id, int $product_id)
    {

        $this->logger_id = $logger_id;
        $this->product_id = $product_id;
    }

    public function handle(ParserService $serviceParser, ProductService $serviceProduct): void
    {
        /** @var Product $product */
        $product = Product::find($this->product_id);
        $logger = LoggerCron::find($this->logger_id);

        $parser_product = ProductParser::where('product_id', $product->id)->first();
        if (is_null($parser_product)) { //Товар есть, в таблице парсера нет.
            try {
                $parser_product_data = $serviceParser->parsingData($product->code_search);
                $parser_product = $serviceParser->createProductParsing($product->id, $parser_product_data);
                $serviceProduct->setCostProductIkea($product->id, 'Парсинг - ' . now());

            } catch (\DomainException $e) {
                $logger->items()->create([
                    'object' => $product->code,
                    'action' => 'Не спарсился',
                    'value' => '',
                ]);
                $serviceProduct->CheckNotSale($product); //Убираем из продажи
                return;
            } catch (\Throwable $e) {
                event(new ThrowableHasAppeared($e));
            }
        }

        $price = $serviceParser->parserCost($product->code_search); //В Злотах
        if ($price < 0) {//Цена не парсится, убираем из продажи
            $parser_product->block();
            $serviceProduct->CheckNotSale($product);

        } else {
            //Проверка цены с таблицы Парсера
            if (!$parser_product->priceEquivalent($price)) { //Цена изменилась
                $parser_product->setCost($price);
                $serviceProduct->setCostProductIkea($product->id, 'Парсинг - ' . now());
            }
            if (!$product->isSale()) $product->setForSale(); //Цена на Икеа парсится, а товар снят с продажи, значит возвращаем
        }
    }

    public function isIkea($code, $code_search): bool
    {
        if (strlen($code) != 10) return false;
        if (strlen($code_search) != 8) return false;
        if (!is_numeric($code_search)) return false;
        return true;
    }
}
