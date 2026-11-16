<?php

declare(strict_types=1);

namespace App\Modules\Marketplace\Infrastructure\Jobs;

use App\Modules\Marketplace\Application\Services\Avito\AvitoAPIService;
use App\Modules\Marketplace\Infrastructure\Models\AvitoProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Получает avito_id объявления через API и сохраняет его в поле ad_number.
 */
class UpdateAvitoProductIdNumberJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $avitoProductId,
        public readonly string $adId,
    ) {}

    public function handle(AvitoAPIService $apiService): void
    {
        $avitoId = $apiService->getAvitoIdByAdId($this->adId);

        if ($avitoId === null) {
            return;
        }

        AvitoProduct::where('id', $this->avitoProductId)
            ->update(['ad_number' => $avitoId]);
    }
}
