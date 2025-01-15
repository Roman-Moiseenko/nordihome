<?php
declare(strict_types=1);

namespace App\Console\Commands\Telegram;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Notification\Helpers\NotificationHelper;
use App\Modules\Notification\Helpers\TelegramParams;
use App\Modules\Notification\Message\StaffMessage;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'telegram:test';
    protected $description = 'Отправить тестовое сообщение в чат c подтверждением';
    public function handle()
    {
        /** @var Admin[] $admins */
        $admins = Admin::where('telegram_user_id', '>', 0)->get();

        $params = new TelegramParams( TelegramParams::OPERATION_READ, null);

        foreach ($admins as $admin) {
            $admin->notify(new StaffMessage(
                NotificationHelper::EVENT_TEST,
                'Необходимо подтвердить получения тестового сообщения',
                '',
                $params
            ));
        }
        return true;
    }
}
