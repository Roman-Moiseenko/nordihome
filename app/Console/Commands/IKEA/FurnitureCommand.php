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

    const string BLUM = 'BLUM';
    const string GTV = 'GTV';

    public function handle(GoogleSheetService $googleSheet): void
    {

        $rows = $googleSheet->getFurnitureRows(self::BLUM);
        $delta = 0;
        foreach ($rows as $number => $row) {
            if (!empty($code = $row['АРТИКУЛ'])) {
                //Берем код до 1го пробела
                $words = explode(' ', $code);

                $this->info($words[0] . ' отправлен в очередь. Задержка - ' . $number * 2 . ' с');
                FurnitureParser::dispatch($code, $number, self::BLUM, 'H')->delay(now()->addSeconds($number * 2));
                $delta += $number * 2;
            }
        }
        $delta++;
        $rows = $googleSheet->getFurnitureRows(self::GTV);
        foreach ($rows as $number => $row) {
            if (!empty($code = $row['АРТИКУЛ'])) {
                //Берем код до 1го пробела
                $words = explode(' ', $code);

                $this->info($words[0] . ' отправлен в очередь. Задержка - ' . $number * 2 . ' с');
                FurnitureParser::dispatch($code, $number, self::GTV, 'G')->delay(now()->addSeconds($delta + $number * 2));
            }
        }



    }

}
