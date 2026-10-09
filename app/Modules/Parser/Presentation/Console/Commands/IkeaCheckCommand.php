<?php

namespace App\Modules\Parser\Presentation\Console\Commands;

use App\Console\CreatesApplication;
use App\Modules\Parser\Application\Services\LoadParserProductIkeaService;
use App\Modules\Parser\Infrastructure\Services\IkeaProductApi;
use Illuminate\Console\Command;

/**
 * Парсим новые (!) товары из каталогов Икеа
 */
class IkeaCheckCommand extends Command
{
    use CreatesApplication;
    protected $signature = 'ikea:check';
    protected $description = 'Проверка соединения';

    public function handle(IkeaProductApi $api): void
    {

        $code = '40178888';
        //$code = '59434138';
        $productData = $api->getProductByCode($code);

        if (is_null($productData)) {
            $this->warn('Нет связи');
            return;
        }

       // dd($productData['gprDescription']['variants'][0]['allProductImage']);
        $this->info($productData['pipUrl']);
        $dataPage = $api->getProductPage($productData['pipUrl']);
        dd($dataPage);
    }
}
