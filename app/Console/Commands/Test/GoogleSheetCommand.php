<?php

namespace App\Console\Commands\Test;

use App\Modules\Bank\Service\YookassaService;
use App\Modules\Nordihome\Service\FunctionService;
use App\Modules\Nordihome\Service\GoogleSheetService;
use Illuminate\Console\Command;
use Tests\CreatesApplication;

class GoogleSheetCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'google:sheet';
    protected $description = 'Тестируем работу с гугл таблицами';

    public function handle(GoogleSheetService $service)
    {
        $rows = $service->getFurnitureRows();
        dd($rows);
    }
}
