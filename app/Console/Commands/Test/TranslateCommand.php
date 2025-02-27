<?php


namespace App\Console\Commands\Test;


use App\Modules\Base\Service\YandexTranslate;
use App\Modules\Shop\Parser\ParserService;
use Illuminate\Console\Command;
use Tests\CreatesApplication;

class TranslateCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'translate';
    protected $description = 'Тестирования Яндекс переводчика';

    public function handle()
    {
        $text = 'Spodnie dresowe męskie są niezbędnikiem każdego aktywnego mężczyzny, niezależnie od tego, jaką formę sportu preferuje. Dresy męskie to bardzo wygodne rozwiązanie nie tylko na trening – coraz chętniej wybierane są także na co dzień, jako alternatywa dla jeansów';
        $this->info($text);

        $translate = YandexTranslate::translate($text);
        $this->info($translate);
        //dd($product->packages);

    }

}
