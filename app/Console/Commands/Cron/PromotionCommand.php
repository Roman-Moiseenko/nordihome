<?php
declare(strict_types=1);

namespace App\Console\Commands\Cron;

use App\Events\PromotionHasMoved;
use App\Modules\Discount\Entity\Promotion;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PromotionCommand extends Command
{
    protected $signature = 'cron:promotion';
    protected $description = 'Смена статусов в Акциях';

    public function handle()
    {

        //поиск акций, которые стартуют через 3 дня, запускаем событие
        $promotions = Promotion::where('published', true)->where('active', true)->where('start_at', '=', Carbon::now()->addDays(3)->toDateString())->get();
        foreach ($promotions as $promotion) {
            event(new PromotionHasMoved($promotion));
        }
        //Старт акция
        $promotions = Promotion::where('published', true)->where('active', false)->where('start_at', '<=', Carbon::now()->toDateString())->get();
        foreach ($promotions as $promotion) {
            $promotion->start();
            $promotion->start_at = now();
            $promotion->save();
            event(new PromotionHasMoved($promotion));
        }
        //поиск акций, которые закончатся через 3 дня, запускаем событие
        $promotions = Promotion::where('published', true)->where('active', true)->where('finish_at', '=', Carbon::now()->addDays(3)->toDateString())->get();
        foreach ($promotions as $promotion) {
            event(new PromotionHasMoved($promotion));
        }
        //Завершение акций
        $promotions = Promotion::where('published', true)->where('active', true)->where('finish_at', '<=', Carbon::now()->toDateString())->get();
        foreach ($promotions as $promotion) {
            $promotion->finish();
            $promotion->save();
            event(new PromotionHasMoved($promotion));
        }
    }
}
