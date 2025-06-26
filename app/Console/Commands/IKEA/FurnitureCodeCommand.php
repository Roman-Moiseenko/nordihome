<?php

namespace App\Console\Commands\IKEA;

use App\Modules\Nordihome\Service\FunctionService;
use App\Modules\Nordihome\Service\FurnitureService;
use App\Modules\Nordihome\Service\GoogleSheetService;
use App\Modules\Parser\Job\FurnitureParser;
use Illuminate\Console\Command;
use Tests\CreatesApplication;

class FurnitureCodeCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'furniture:code {code} {web}';
    protected $description = 'Парсить цены фурнитуры';


    public function handle(FurnitureService   $furnitureService): void
    {
        $code = $this->argument('code');
        $this->info("Артикул товара = " . $code);

        $web = $this->argument('web');
        if ($web == 1) $price_2 = $furnitureService->getBaltlaminat($code);
        if ($web == 2) $price_2 = $furnitureService->getHolzMaster($code);
        $this->info($price_2);

    }

}
