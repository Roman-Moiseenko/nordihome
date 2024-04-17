<?php
declare(strict_types=1);

namespace App\Console\Commands\Cron;

use App\Events\ProductHasPublished;
use Illuminate\Console\Command;

class ProductCommand extends Command
{
    protected $signature = 'cron:product-new';
    protected $description = 'Рассылка по новым товарам';

    public function handle()
    {
        event(new ProductHasPublished());
    }
}
