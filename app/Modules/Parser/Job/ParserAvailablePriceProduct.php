<?php
declare(strict_types=1);

namespace App\Modules\Parser\Job;

use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Page\Job\JobCacheProduct;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Parser\Service\ParserAbstract;
use App\Modules\Parser\Service\ParserIkea;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

/**
 * Повторное спарисание товара уже имеющегося в БД (прлверка цены и доступности)
 */
class ParserAvailablePriceProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $logger_id;
    private int $parser_product_id;

    public function __construct(int $logger_id, int $parser_product_id)
    {
        $this->logger_id = $logger_id;
        $this->parser_product_id = $parser_product_id;
    }


    public function handle(ParserIkea $parserIkea): void
    {
        try {
            $product_parser = ProductParser::find($this->parser_product_id);

            if ($parserIkea->parserCost($product_parser)) {
                JobCacheProduct::dispatch($product_parser->product_id);
            }
        } catch (\Throwable $e) {
            $logger = LoggerCron::find($this->logger_id);
            $logger->items()->create([
                'object' => $this->parser_product_id,
                'action' => 'Не спарсился',
                'value' => json_encode([$e->getMessage(), $e->getFile(), $e->getLine()]),
            ]);
        }
    }
}
