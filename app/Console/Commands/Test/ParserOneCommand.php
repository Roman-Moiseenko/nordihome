<?php


namespace App\Console\Commands\Test;


use App\Modules\Shop\Parser\ParserService;
use Illuminate\Console\Command;
use Tests\CreatesApplication;

class ParserOneCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'parser {code}';
    protected $description = 'Парсим товар по артикулу';

    public function handle(ParserService $service)
    {
        $code = $this->argument('code');

        $product = $service->findProduct($code);
        $this->info($product->name);
        //dd($product->packages);

    }

}
