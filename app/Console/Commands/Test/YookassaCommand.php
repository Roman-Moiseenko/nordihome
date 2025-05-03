<?php

namespace App\Console\Commands\Test;

use App\Modules\Bank\Service\YookassaService;
use Illuminate\Console\Command;
use Tests\CreatesApplication;

class YookassaCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'kassa:test';
    protected $description = 'Тестируем касса';

    public function handle(YookassaService $service)
    {
        $payment = $service->test();
     /*   dd([
            $payment['id'],
            $payment['status'],
            $payment['confirmation']['confirmation_url'],
            $payment['metadata']
        ]); */

        dd($payment);
    }
}
