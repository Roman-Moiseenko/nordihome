<?php

namespace App\Console\Commands\IKEA;


use App\Modules\Parser\Application\Services\LoadParserProductIkeaService;
use Illuminate\Console\Command;
use App\Console\CreatesApplication;

class ParserCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'parser:find';
    protected $description = 'Парсить икеа - тест';



    public function handle(LoadParserProductIkeaService $service): void
    {

        //$code = $this->ask('Артикул товара');

        $code = '903.493.26'; // - сложный
        //$code = '194.948.41'; // - составной
    //    $code = '40178888'; // - одинарный

        $product = $service->FindByCode($code);
        $this->info(json_encode($product));


    }

}
