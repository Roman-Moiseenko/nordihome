<?php
declare(strict_types=1);

namespace App\Modules\Base\Job;

use App\Modules\Base\Entity\Photo;
use App\Modules\Product\Entity\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConvertPhotoProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Photo $photo;

    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
    }

    public function handle(): void
    {
        $this->photo->convertToWebp();
    }
}
