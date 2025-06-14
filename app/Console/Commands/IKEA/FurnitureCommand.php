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

    const string BLUM = 'BLUM НГЦ';
    const string GTV = 'GTV НГЦ';

    public function handle(GoogleSheetService $googleSheet): void
    {

        $rows = $googleSheet->getFurnitureRows(self::BLUM);
        foreach ($rows as $number => $row) {
            if (!empty($code = $row['АРТИКУЛ'])) {
                //Берем код до 1го пробела
                $words = explode(' ', $code);

                $this->info($words[0] . ' отправлен в очередь. Задержка - ' . $number * 2 . ' с');
                FurnitureParser::dispatch($code, $number, self::BLUM, 'H')->delay(now()->addSeconds($number * 2));
            }
        }
        $delta = count($rows) * 2 + 2;
        $rows = $googleSheet->getFurnitureRows(self::GTV);
        foreach ($rows as $number => $row) {
            if (!empty($code = $row['АРТИКУЛ'])) {
                //Берем код до 1го пробела
                $words = explode(' ', $code);

                $this->info($words[0] . ' отправлен в очередь. Задержка - ' . ($delta + $number * 2) . ' с');
                FurnitureParser::dispatch($code, $number, self::GTV, 'E')->delay(now()->addSeconds($delta + $number * 2));
            }
        }



    }

}
