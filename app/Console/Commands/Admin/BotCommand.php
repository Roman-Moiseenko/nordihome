<?php
declare(strict_types=1);

namespace App\Console\Commands\Admin;

use Illuminate\Console\Command;
use NotificationChannels\Telegram\TelegramUpdates;

class BotCommand extends Command
{
    protected $signature = 'admin:bot';
    protected $description = 'Получить id сотрудников, подключивших чат-бот';
    public function handle()
    {
        $updates = TelegramUpdates::create()->limit(2)
            ->options([
                'timeout' => 0,
            ])->get();
        //$this->info(json_encode($updates));
        if($updates['ok']) {
            foreach ($updates['result'] as $user) {
                $this->info($user['message']['chat']['first_name'] . ' (' . $user['message']['chat']['username'] . ') - ' . $user['message']['chat']['id']);
            }
        }
        return true;
    }
}
