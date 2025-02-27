<?php
declare(strict_types=1);

namespace App\Modules\Page\Job;

use App\Modules\Base\Helpers\CacheHelper;
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

class JobCacheCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   // private int $page;
    private string $slug;

    public function __construct(string $slug)
    {
      //  $this->page = $page;
        $this->slug = $slug;
    }

    public function handle(CacheRepository $cacheRepository): void
    {
        try {
         //   Cache::forget('category-' . $this->slug . '-' . $this->page);
         //   Cache::forget('category-' . $this->slug . '-0');
         //   Cache::forget('category-' . $this->slug . '-1');
          //  Cache::forget('category-' . $this->slug);
            foreach (CacheHelper::CATEGORIES as $CATEGORY) {
                Cache::forget($CATEGORY . $this->slug);
            }

            ///Cache::forget(CacheHelper::CATEGORY_ATTRIBUTES . $this->slug);
            $cacheRepository->category([], $this->slug);

        } catch (\Throwable $e) {
            Log::error(json_encode([$e->getMessage(), $e->getLine(), $e->getFile()]));
        }
    }

}
