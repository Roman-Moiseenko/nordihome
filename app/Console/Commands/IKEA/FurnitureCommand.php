<?php

namespace App\Console\Commands\IKEA;

use App\Modules\Nordihome\Service\FunctionService;
use App\Modules\Nordihome\Service\GoogleSheetService;
use App\Modules\Parser\Job\FurnitureParser;
use Illuminate\Console\Command;
use Tests\CreatesApplication;

class FurnitureCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'furniture';
    protected $description = 'Парсить цены фурнитуры';

    public function handle(GoogleSheetService $googleSheet): void
    {
        $rows = $googleSheet->getFurnitureRows();
        foreach ($rows as $number => $row) {
            if (!empty($code = $row['АРТИКУЛ'])) {
                //Берем код до 1го пробела
                $words = explode(' ', $code);

                $this->info($words[0] . ' отправлен в очередь');
                FurnitureParser::dispatch($code, $number);
            }
        }

    }

}
