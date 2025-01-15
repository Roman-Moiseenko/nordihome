<?php
declare(strict_types=1);

namespace App\Modules\Notification\Repository;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Employee\Entity\Employee;
use App\Modules\Notification\Entity\TelegramRead;
use App\Modules\Notification\Helpers\NotificationHelper;
use App\Modules\Notification\Message\StaffMessage;

class TelegramRepository
{

    public function getUserByTelegram(mixed $telegram_user_id)
    {
        //Сотрудник?
        $user = Admin::where('telegram_user_id', $telegram_user_id)->first();
        if (is_null($user)) {
            //Персонал?
            $user = Worker::where('telegram_user_id', $telegram_user_id)->first();
            if (is_null($user)) {
                //TODO Сообщение Админу на блокировку $telegram_user_id
                throw new \DomainException('Посторонний в чате ' . $telegram_user_id);
            }
        }
        return $user;
    }

    public function checkMessage(int $message_id, int $telegram_user_id, string $message)
    {
        $tg = TelegramRead::where('message_id', $message_id)->where('telegram_user_id', $telegram_user_id)->first();
        if (is_null($tg)) {
            TelegramRead::register($message_id, $telegram_user_id, $message);
        } else {
            $user = $this->getUserByTelegram($telegram_user_id);
            $user->notify(new StaffMessage(
                NotificationHelper::EVENT_ERROR,
                'Вы уже подтвердили сообщение!',
            ));
            throw new \DomainException('Повторное нажатие на подтверждение!');
        }
    }
}
