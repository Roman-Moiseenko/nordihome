<?php

declare(strict_types=1);

namespace App\Modules\Shared\Infrastructure\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveTempPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $fileName,
    )
    {
    }

    public function handle(): void
    {
        if (is_file($this->fileName)) {
            unlink($this->fileName);
        }
    }
}
