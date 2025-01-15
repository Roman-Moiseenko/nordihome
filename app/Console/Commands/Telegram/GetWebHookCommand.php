<?php
declare(strict_types=1);

namespace App\Console\Commands\Telegram;

use App\Modules\Notification\Service\TelegramService;
use Illuminate\Console\Command;

class GetWebHookCommand extends Command
{
    protected $signature = 'telegram:get-webhook';

    protected $description = 'Проверка прослушивания чат-бота Телеграм';

    public function handle(TelegramService $service): bool
    {
        $result = $service->getWebHook();
        $this->info(json_encode($result));
        return true;
    }
}
