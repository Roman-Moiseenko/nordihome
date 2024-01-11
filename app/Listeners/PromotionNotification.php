<?php

namespace App\Listeners;

use App\Events\PromotionHasMoved;
use App\Mail\PromotionFinished;
use App\Mail\PromotionFinishing;
use App\Mail\PromotionStarted;
use App\Mail\PromotionStarting;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\User\Entity\User;
use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;

class PromotionNotification
{
    private $users;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //TODO Получить список Пользователей, которые подписались на рассылку
        $this->users = User::where('status', User::STATUS_ACTIVE)->get();
    }

    /**
     * Handle the event.
     */
    public function handle(PromotionHasMoved $event): void
    {
        //Определяем тип Акции
        if ($event->promotion->status() == Promotion::STATUS_WAITING && $event->promotion->start_at == Carbon::now()->addDays(3)->toDateString()) {
            //За три дня до начала
            foreach($this->users as $user) {
                Mail::to($user->email)->queue(new PromotionStarting($event->promotion));
            }
        }
        if ($event->promotion->status() == Promotion::STATUS_STARTED && $event->promotion->start_at == Carbon::now()->toDateString()) {
            //Старт
            foreach($this->users as $user) {
                Mail::to($user->email)->queue(new PromotionStarted($event->promotion));
            }
        }
        if ($event->promotion->status() == Promotion::STATUS_STARTED && $event->promotion->finish_at == Carbon::now()->addDays(3)->toDateString()) {
            //За три дня до окончания
            foreach($this->users as $user) {
                Mail::to($user->email)->queue(new PromotionFinishing($event->promotion));
            }
        }
        if ($event->promotion->status() == Promotion::STATUS_FINISHED && $event->promotion->finish_at == Carbon::now()->toDateString()) {
            //Финиш
            foreach($this->users as $user) {
                Mail::to($user->email)->queue(new PromotionFinished($event->promotion));
            }
        }

    }
}
