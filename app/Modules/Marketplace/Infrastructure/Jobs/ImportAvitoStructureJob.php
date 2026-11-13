<?php

namespace App\Modules\Marketplace\Infrastructure\Jobs;

use App\Modules\Marketplace\Application\Services\Avito\AvitoStructureImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportAvitoStructureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $filePath,
    ) {}

    public function handle(AvitoStructureImportService $service): void
    {
        $service->import($this->filePath);

        if (is_file($this->filePath)) {
            @unlink($this->filePath);
        }
    }
}
