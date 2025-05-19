<?php

namespace App\Console\Commands\IKEA;

use App\Modules\Nordihome\Service\FunctionService;
use App\Modules\Nordihome\Service\FurnitureService;
use App\Modules\Nordihome\Service\GoogleSheetService;
use App\Modules\Parser\Job\FurnitureParser;
use Illuminate\Console\Command;
use Tests\CreatesApplication;

class TestCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'furniture:test';
    protected $description = 'Парсить цены фурнитуры';

    const string BLUM = 'BLUM';
    const string GTV = 'GTV';

    public function handle(GoogleSheetService $googleSheet,
                           FurnitureService   $furnitureService): void
    {

  /*      $rows = $googleSheet->getFurnitureRows(self::BLUM);
    //    $this->info(json_encode($rows[0]['АРТИКУЛ']));

        foreach ($rows as $number => $row) {
            if (!empty($code = $row['АРТИКУЛ'])) {
                //Берем код до 1го пробела
                $words = explode(' ', $code);

                $this->info($words[0] . ' Парсим ');
                $price_1 = $furnitureService->getHolzMaster($code);

                $price_2 = $furnitureService->getBaltlaminat($code);
                $this->info($price_1 . '  ' . $price_2);
            }
        }

*/
        $rows = $googleSheet->getFurnitureRows(self::GTV);
        $this->info('Строк - ' . count($rows));
        $this->info(json_encode($rows[0]['АРТИКУЛ']));

        foreach ($rows as $number => $row) {
            if (!empty($code = $row['АРТИКУЛ'])) {
                //Берем код до 1го пробела
                $words = explode(' ', $code);
                $this->info($words[0] . ' Парсим ');
                $price_1 = $furnitureService->getHolzMaster($code);
                /// Парсим сайт 2, получаем список урлов
                $price_2 = $furnitureService->getBaltlaminat($code);
                $this->info($price_1 . '  ' . $price_2);

            }
        }



    }

}
