<?php

namespace App\Modules\Base\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearTempFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $path_file;

    /**
     * Create a new job instance.
     */
    public function __construct(string $path_file)
    {

        $this->path_file = $path_file;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (is_file($this->path_file)) {
            unlink($this->path_file);
        }
    }
}
