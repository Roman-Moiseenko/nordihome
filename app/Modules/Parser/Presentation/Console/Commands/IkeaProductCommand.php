<?php

namespace App\Modules\Parser\Presentation\Console\Commands;

use App\Console\CreatesApplication;
use App\Modules\Parser\Application\Services\LoadParserProductIkeaService;
use Illuminate\Console\Command;

/**
 * Парсим новые (!) товары из каталогов Икеа
 */
class IkeaProductCommand extends Command
{
    use CreatesApplication;
    protected $signature = 'ikea:products';
    protected $description = 'Парсим Товары Икеа';

    public function handle(LoadParserProductIkeaService $service): void
    {
        $service->load();
    }
}
