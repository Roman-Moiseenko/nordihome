<?php
declare(strict_types=1);

namespace App\Modules\Admin\Listeners;

use App\Modules\Order\Events\ExpenseHasCompleted;

class NotificationStaff
{
    //TODO Добавлять events
    public function handle(ExpenseHasCompleted $event): void
    {
        //TODO Уведомляем сотрудника
    }

}
