<?php

namespace App\Modules\Base\Job;

use App\Modules\Parser\Entity\CategoryParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LoadingImageCatalog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private CategoryParser $category;
    private string $image_url;

    public function __construct(CategoryParser $category, string $image_url)
    {
        $this->category = $category;
        $this->image_url = $image_url;
    }

    /**
     * Скачивание изображения для товара по ссылке
     */
    public function handle(): void
    {
        $photo = $this->category->addImageByUrl($this->image_url);
        if (!is_null($photo)) {
            if (pathinfo($photo->file, PATHINFO_EXTENSION) != 'webp') $photo->convertToWebp();
        } else {
            Log::error('Фото не загружено ' . $this->category->id . ' ' . $this->image_url);
        }
    }
}
