<?php

namespace App\Console\Commands\Test;

use App\Console\CreatesApplication;
use App\Modules\Mail\Entity\MailTemplateRegistry;
use App\Modules\Shared\Application\Interfaces\Mail\MailServiceInterface;
use App\Modules\Shared\Domain\Entities\Mail\Recipient;
use Illuminate\Console\Command;

class MailTestCommand extends Command
{
    use CreatesApplication;


    protected $signature = 'mail:test';
    protected $description = 'Тестируем работу с гугл таблицами';

    public function handle(MailServiceInterface $mailService)
    {
        try {

            $template = MailTemplateRegistry::get('test');
            $mailService->send(
                $template,
                [],
                new Recipient(email: 'saint_johnny', clientId: null)
            );
        } catch (\Throwable $e) {
            \Log::warning($e->getMessage());
        }
    }
}

