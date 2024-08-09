<?php
declare(strict_types=1);

namespace App\Console\Commands\Cron;

use App\Jobs\ParserProduct;
use App\Modules\Shop\Parser\ParserService;
use Illuminate\Console\Command;
use Tests\CreatesApplication;

class ParsertestCommand extends Command
{

    use CreatesApplication;

    protected $signature = 'parser:test';

    protected $description = '';
    /**
     * @var ParserService|mixed
     */
    private mixed $service;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //Отмененные категории
        /*
            'he001',
            'hs001',
            'fb001',
        */
        $products = [
            //'bm001',
            '10579401',

        ]; //Url-ы категорий

        $app = $this->createApplication();
        $service = $app->make(ParserService::class);

        $count = $service->parsingQuantity('10579401');
        $this->info(json_encode($count));

        return true;
    }

    public function toJob(array $products)
    {
        foreach ($products as $product) {
            $code = $product['itemNo'];
            ParserProduct::dispatch($code);
        }
        $this->info('**** В очередь отправлены');
    }

}
