<?php

namespace App\Console\Commands\IKEA;

use App\Modules\Nordihome\Service\FunctionService;
use App\Modules\Nordihome\Service\FurnitureService;
use App\Modules\Nordihome\Service\GoogleSheetService;
use App\Modules\Parser\Job\FurnitureParser;
use App\Modules\Parser\Service\ParserIkea;
use Illuminate\Console\Command;
use App\Console\CreatesApplication;

class ParserCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'parser:find';
    protected $description = 'Парсить икеа - тест';



    public function handle(ParserIkea $parserIkea): void
    {

        //$code = $this->ask('Артикул товара');

        $code = '903.493.26'; // - сложный
        //$code = '194.948.41'; // - составной
    //    $code = '40178888'; // - одинарный

        $product = $parserIkea->findProduct($code);
        $this->info(json_encode($product));


    }

}
