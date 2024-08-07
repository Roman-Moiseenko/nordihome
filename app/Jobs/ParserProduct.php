<?php

namespace App\Jobs;

use App\Modules\Shop\Parser\ParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Tests\CreatesApplication;

class ParserProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $code;

    /**
     * Create a new job instance.
     */
    public function __construct(string $code)
    {
        $this->code = $code;
    }

    /**
     * Execute the job.
     */
    public function handle(ParserService $service): void
    {
        $service->findProduct($this->code);
    }
}
