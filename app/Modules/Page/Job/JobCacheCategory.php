<?php
declare(strict_types=1);

namespace App\Modules\Page\Job;

use App\Modules\Base\Helpers\CacheHelper;
use App\Modules\Shop\Repository\CacheRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class JobCacheCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $slug;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    public function handle(CacheRepository $cacheRepository): void
    {
        try {
            foreach (CacheHelper::CATEGORIES as $CATEGORY) {
                Cache::forget($CATEGORY . $this->slug);
            }
            if ($this->slug == 'root') {
                $cacheRepository->root([]);
            } else {
                $cacheRepository->category([], $this->slug);
            }
        } catch (\Throwable $e) {
            Log::error('JobCacheCategory - ' . $this->slug . ' ' . json_encode([$e->getMessage(), $e->getLine(), $e->getFile()]));
        }
    }

}
