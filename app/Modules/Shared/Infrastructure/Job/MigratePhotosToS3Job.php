<?php

declare(strict_types=1);

namespace App\Modules\Shared\Infrastructure\Job;

use App\Modules\Shared\Application\Actions\MigratePhotosToS3UseCase;
use App\Modules\Shared\Application\DTOs\JobPhotoMigrateData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MigratePhotosToS3Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly JobPhotoMigrateData $dto,
    )
    {
    }

    public function handle(MigratePhotosToS3UseCase $useCase): void
    {
        $useCase->execute($this->dto->paths, $this->dto->removeSource);
    }
}
