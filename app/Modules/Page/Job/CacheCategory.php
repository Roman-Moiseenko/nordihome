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

class CacheCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private int $page;
    private string $slug;

    public function __construct(int $page, string $slug)
    {

        $this->page = $page;
        $this->slug = $slug;
    }

    public function handle(CacheRepository $cacheRepository): void
    {
        try {

            $cache_name = 'category-' . $this->slug . '-' . $this->page;
            Cache::forget($cache_name);
            Cache::rememberForever($cache_name, function () use ($cacheRepository) {
                return $cacheRepository->category_cache(['page' => $this->page], $this->slug);
            });

        } catch (\Throwable $e) {
            Log::error(json_encode([$e->getMessage(), $e->getLine(), $e->getFile()]));
        }
    }

}
