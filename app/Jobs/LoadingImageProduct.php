<?php

namespace App\Jobs;

use App\Modules\Base\Entity\Photo;
use App\Modules\Product\Entity\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LoadingImageProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Product $product;
    private string $image_url;
    private string $image_alt;

    public function __construct(Product $product, string $image_url, string $image_alt = '')
    {
        $this->product = $product;
        $this->image_url = $image_url;
        $this->image_alt = $image_alt;
    }

    /**
     * Скачивание изображения для товара по ссылке
     */
    public function handle(): void
    {
        $photo = $this->product->addImageByUrl($this->image_url);
        $this->product->setAlt(photo_id: $photo->id, alt: $this->image_alt);
    }
}
