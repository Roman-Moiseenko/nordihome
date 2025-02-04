<?php
declare(strict_types=1);

namespace App\Modules\Admin\Listeners;

use App\Modules\Order\Events\ExpenseHasCompleted;

class NewTaskStaff
{
    //TODO Добавлять events
    public function handle(ExpenseHasCompleted $event): void
    {
        //TODO Создать задачу для сотрудника сотрудника
    }
}
