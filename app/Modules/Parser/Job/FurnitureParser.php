<?php

namespace App\Modules\Parser\Job;

use App\Modules\Nordihome\Service\FurnitureService;
use App\Modules\Nordihome\Service\GoogleSheetService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FurnitureParser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $code;
    private int $number;

    public function __construct(string $code, int $number)
    {
        $this->code = $code;
        $this->number = $number;
    }

    public function handle(GoogleSheetService $googleSheet,
                           FurnitureService   $furnitureService): void
    {
        try {
            /// Парсим сайт 1, запоминаем данные
            $price_1 = $furnitureService->getHolzMaster($this->code);
            /// Парсим сайт 2, получаем список урлов
            $price_2 = $furnitureService->getBaltlaminat($this->code);
            //// Парсим урл N
            /// Сохраняем на листе в той же строке данные из сайта 1 и 2
            $googleSheet->setData($this->number + 1, $price_1, $price_2);
        } catch (\Throwable $e) {
            Log::info($this->code);
            Log::info(json_encode([$e->getMessage(), $e->getFile(), $e->getLine()]));
        }


    }

}
