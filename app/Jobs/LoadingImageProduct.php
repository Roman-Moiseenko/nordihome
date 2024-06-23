<?php

namespace App\Jobs;

use App\Entity\Photo;
use App\Modules\Product\Entity\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoadingImageProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Product $product;
    private string $image_url;
    private string $image_alt;
    private bool $proxy;

    public function __construct(Product $product, string $image_url, string $image_alt = '', bool $proxy = false)
    {
        //
        $this->product = $product;
        $this->image_url = $image_url;
        $this->image_alt = $image_alt;
        $this->proxy = $proxy;
    }

    /**
     * Скачивание изображения для товара по ссылке
     */
    public function handle(): void
    {
        $sort = count($this->product->photos);
        if ($this->proxy) {
            $photo = Photo::uploadByUrlProxy($this->image_url, '', $sort, $this->image_alt); //Через прокси
        } else {
            $photo = Photo::uploadByUrl($this->image_url, '', $sort, $this->image_alt);
        }
        $this->product->photo()->save($photo);
    }
}
