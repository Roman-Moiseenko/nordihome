<?php

namespace App\Console\Commands\IKEA;

use App\Modules\Parser\Service\ParserIkea;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use Illuminate\Console\Command;

class StoreCommand extends Command
{
    protected $signature = 'ikea:store';
    protected $description = 'Тестирование кол-во товаров Икеа';

    public function handle(ParserIkea $parser)
    {

        $code = '69509010';
        //$parser->remainsProduct($code);

        $product = $parser->findProduct($code);
        $this->info($product->name);
        return true;


    }
}
