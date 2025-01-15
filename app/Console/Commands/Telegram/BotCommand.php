<?php
declare(strict_types=1);

namespace App\Console\Commands\Telegram;

use App\Modules\Notification\Service\TelegramService;
use Illuminate\Console\Command;
use NotificationChannels\Telegram\TelegramUpdates;

class BotCommand extends Command
{
    protected $signature = 'telegram:bot';
    protected $description = 'Получить id сотрудников, подключивших чат-бот';
    public function handle(TelegramService $service)
    {
        $list = $service->getListChatIds();
        foreach ($list as $item) {
            $this->info($item['name'] . ' ' .
                $item['login'] . ' ' .
                $item['id']
            );
        }
        return true;
    }
}
