<?php
declare(strict_types=1);

namespace App\Console\Commands\Cron;

use App\Jobs\ParserProduct;
use App\Modules\Shop\Parser\ParserService;
use Illuminate\Console\Command;
use Tests\CreatesApplication;

class ParserIkeaCommand extends Command
{

    use CreatesApplication;

    protected $signature = 'cron:parser-product';

    protected $description = 'Спарсить все товары Икеа';
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
        $categories = [
            'bm001',
            'fu001',
            'tl001',
            'st001',
            'bc001',
            'ka001',
            'kt001',
            'od001',
            'de001',
            'ba001',
            'li001',
            'pp001',
            'lc001',
            'rm001',
            'pt001',
            'hi001',
        ]; //Url-ы категорий

        $app = $this->createApplication();
        $this->service = $app->make(ParserService::class);
        foreach ($categories as $category) {
            $this->info('Категория ' . $category);
            $products = [];
            $start = 0;
            $end = 1000;
            do {
                $this->info('старт=' . $start . ' финиш=' . $end);
                $list = $this->service->getProducts($category, $start, $end);
                $this->info('нашлось=' . count($list));
                $start += 1000;
                $end += 1000;
                $products = array_merge($products, $list);
            } while (count($list) == 1000);
            $this->info('Всего нашлось=' . count($products));

            $this->toJob($products);
        }
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
