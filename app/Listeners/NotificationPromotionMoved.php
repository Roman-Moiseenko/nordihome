<?php

namespace App\Listeners;

use App\Events\PromotionHasMoved;

class NotificationPromotionMoved
{
    private array $users;

    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(PromotionHasMoved $event): void
    {
        //FIXME Рассылка по акциям

        /*
        //Определяем тип Акции
        if ($event->promotion->status() == Promotion::WAITING && $event->promotion->start_at->toDateString() == Carbon::now()->addDays(3)->toDateString()) {
            //За три дня до начала
            foreach($this->users as $user) {
                Mail::to($user->email)->queue(new PromotionStarting($event->promotion));
            }
        }
        if ($event->promotion->status() == Promotion::STARTED && $event->promotion->start_at->toDateString() == Carbon::now()->toDateString()) {
            //Старт
            foreach($this->users as $user) {
                Mail::to($user->email)->queue(new PromotionStarted($event->promotion));
            }
        }
        if ($event->promotion->status() == Promotion::STARTED && !is_null($event->promotion->finish_at) && $event->promotion->finish_at->toDateString() == Carbon::now()->addDays(3)->toDateString()) {
            //За три дня до окончания
            foreach($this->users as $user) {
                Mail::to($user->email)->queue(new PromotionFinishing($event->promotion));
            }
        }
        if ($event->promotion->status() == Promotion::FINISHED && $event->promotion->finish_at->toDateString() == Carbon::now()->toDateString()) {
            //Финиш
            foreach($this->users as $user) {
                Mail::to($user->email)->queue(new PromotionFinished($event->promotion));
            }
        }
*/
    }
}
