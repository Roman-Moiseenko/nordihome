<?php

namespace App\Modules\Parser\Infrastructure\Jobs;

use App\Modules\Parser\Application\Services\LoadParserCategoryIkeaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoadCategoryIkeaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly array $categoryData, private readonly ?int $parentId)
    {
    }

    public function handle(LoadParserCategoryIkeaService $loadParserCategoryIkeaService): void
    {
        $loadParserCategoryIkeaService->addCategory($this->categoryData, $this->parentId);
    }
}
