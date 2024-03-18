<?php
declare(strict_types=1);

namespace App\Console\Commands\Cron;

use App\Events\PromotionHasMoved;
use App\Events\ThrowableHasAppeared;
use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Discount\Entity\Promotion;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PromotionCommand extends Command
{
    protected $signature = 'cron:promotion';
    protected $description = 'Смена статусов в Акциях';

    public function handle()
    {
        $logger = LoggerCron::new($this->description);
        $change = false;
        $this->info('Акции - проверка');
        try {
            //поиск акций, которые стартуют через 3 дня, запускаем событие
            /** @var Promotion[] $promotions */
            $promotions = Promotion::where('published', true)->where('active', true)->where('start_at', '=', Carbon::now()->addDays(3)->toDateString())->get();
            foreach ($promotions as $promotion) {
                $change = true;
                $logger->items()->create([
                    'object' => $promotion->name,
                    'action' => '3 дня до старта',
                    'value' => '',
                ]);

                $this->info('3 дня до старта - ' . $promotion->name);

                event(new PromotionHasMoved($promotion));
            }
            //Старт акция
            $promotions = Promotion::where('published', true)->where('active', false)->
            where('start_at', '<=', Carbon::now()->toDateString())->
            where('finish_at', '>', Carbon::now()->toDateString())->
            get();
            foreach ($promotions as $promotion) {
                $change = true;
                $logger->items()->create([
                    'object' => $promotion->name,
                    'action' => 'Старт акции',
                    'value' => '',
                ]);

                $promotion->start();
                $promotion->start_at = now();
                $promotion->save();
                $this->info('Старт - ' . $promotion->name);

                event(new PromotionHasMoved($promotion));
            }
            //поиск акций, которые закончатся через 3 дня, запускаем событие
            $promotions = Promotion::where('published', true)->where('active', true)->where('finish_at', '=', Carbon::now()->addDays(3)->toDateString())->get();
            foreach ($promotions as $promotion) {
                $change = true;
                $logger->items()->create([
                    'object' => $promotion->name,
                    'action' => '3 дня до финиша',
                    'value' => '',
                ]);

                $this->info('3 дня до финиша - ' . $promotion->name);

                event(new PromotionHasMoved($promotion));
            }
            //Завершение акций
            $promotions = Promotion::where('published', true)->where('active', true)->where('finish_at', '<=', Carbon::now()->toDateString())->get();
            foreach ($promotions as $promotion) {
                $change = true;
                $logger->items()->create([
                    'object' => $promotion->name,
                    'action' => 'Финиш акции',
                    'value' => '',
                ]);

                $promotion->finish();
                $promotion->save();
                $this->info('Финиш - ' . $promotion->name);

                event(new PromotionHasMoved($promotion));
            }
            if (!$change) $logger->delete();

        } catch (\Throwable $e) {
            $logger->delete();
            event(new ThrowableHasAppeared($e));
        }
    }
}
