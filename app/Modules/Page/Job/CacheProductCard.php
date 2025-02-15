<?php
declare(strict_types=1);

namespace App\Modules\Page\Job;

use App\Modules\Mail\Mailable\OutboxMail;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\Shop\Repository\CacheRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CacheProductCard implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private int $product_id;

    public function __construct(int $product_id)
    {
        $this->product_id = $product_id;
    }

    public function handle(CacheRepository $cacheRepository)
    {
        try {
            $product = Product::find($this->product_id);
            Cache::forget('product-card-' . $this->product_id);
            $cacheRepository->product_card_cache($product);
        } catch (\Throwable $e) {
            Log::error(json_encode([$e->getMessage(), $e->getLine(), $e->getFile()]));
        }
    }

}
