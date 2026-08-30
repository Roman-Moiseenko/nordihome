<?php

namespace App\Modules\Catalog\Presentation\Console\Commands;

use App\Modules\Catalog\Infrastructure\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class ResetPublishedCommand extends Command
{
    use ConfirmableTrait;
    protected $signature = 'published:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Сброс даты публикации на 01.01.2026';

    public function handle(): void
    {
        if (! $this->confirmToProceed()) return;


        Product::where('published', true)->update(['published_at' => Carbon::create(2026, 1, 1)]);

        $this->info('Дата публикации изменена на 01.01.2026');
    }
}
