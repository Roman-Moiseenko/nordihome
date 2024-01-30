<?php

namespace App\Listeners;

use App\Entity\Admin;
use App\Events\ProductHasParsed;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use NotificationChannels\Telegram\TelegramUpdates;

class NotificationNewProductParser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductHasParsed $event): void
    {
        //TODO уведомление сотрудникам что новый товар
        $staffs = Admin::where('role', Admin::ROLE_COMMODITY)->get();
        $message = "Добавлен новый товар через Парсер\n Артикул товара " . $event->product->code;
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage($message));
        }
    }
}
