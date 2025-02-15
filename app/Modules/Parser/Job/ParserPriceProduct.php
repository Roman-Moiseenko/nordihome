<?php
declare(strict_types=1);

namespace App\Modules\Parser\Job;

use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Page\Job\CacheProductCard;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Parser\Service\ParserAbstract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ParserPriceProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $logger_id;
    private int $parser_product_id;

    public function __construct(int $logger_id, int $parser_product_id)
    {
        $this->logger_id = $logger_id;
        $this->parser_product_id = $parser_product_id;
    }

    public function handle(): void
    {
        $logger = LoggerCron::find($this->logger_id);
        try {
            /** @var ProductParser $product_parser */
            $product_parser = ProductParser::find($this->parser_product_id);
            $brand = $product_parser->product->brand;
            $parser_class = $brand->parser_class;
            /** @var ParserAbstract $parser */
            $parser = app()->make($parser_class);

            $price = $parser->parserCost($product_parser);
            if ($price > 0) {
                $logger->items()->create([
                    'object' => $product_parser->product->code,
                    'action' => 'Цена спарсилась',
                    'value' => $price,
                ]);
            } elseif ($price < 0 ) {
                $logger->items()->create([
                    'object' => $product_parser->product->code,
                    'action' => 'Товар не доступен больше',
                ]);
            }
            if ($price != 0) CacheProductCard::dispatch($product_parser->product_id);
        } catch (\Throwable $e) {
            $logger->items()->create([
                'object' => $this->parser_product_id,
                'action' => 'Не спарсился',
                'value' => json_encode([$e->getMessage(), $e->getFile(), $e->getLine()]),
            ]);
        }




    }
}
