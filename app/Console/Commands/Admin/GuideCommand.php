<?php
declare(strict_types=1);

namespace App\Console\Commands\Admin;

use App\Modules\Guide\Entity\Addition;
use App\Modules\Guide\Entity\Country;
use App\Modules\Guide\Entity\MarkingType;
use App\Modules\Guide\Entity\Measuring;
use App\Modules\Guide\Entity\VAT;
use App\Modules\Order\Entity\Addition\AssemblyCalculate;
use App\Modules\Order\Entity\Addition\DeliveryPolandCalculate;
use App\Modules\Order\Entity\Addition\LiftingCalculate;
use App\Modules\Order\Entity\Addition\PackingCalculate;
use Illuminate\Console\Command;
use NotificationChannels\Telegram\TelegramUpdates;

class GuideCommand extends Command
{
    protected $signature = 'guide:fill';
    protected $description = 'Первоначальное заполнение справочников';
    public function handle(): true
    {
        $this->info('Справочник - guide_country');
        Country::create(['name' => 'Россия']);
        Country::create(['name' => 'Китай']);
        Country::create(['name' => 'Польша']);
        Country::create(['name' => 'Вьетнам']);
        Country::create(['name' => 'Тайланд']);
        Country::create(['name' => 'Индия']);

        $this->info('Справочник - guide_measuring');
        Measuring::register('шт', '795');
        Measuring::register('пачка', '728');
        Measuring::register('уп', '778');
        Measuring::register('кг', '166', true, 'г');
        Measuring::register('м', '006', true, 'мм');


        Measuring::register('г', '163');
        Measuring::register('т', '534', true, 'кг');
        Measuring::register('км','008', true, 'м');
        Measuring::register('п.м.', '018');



        Measuring::create(['name' => 'г']);
        Measuring::create(['name' => 'т', 'fractional' => true]);
        Measuring::create(['name' => 'км', 'fractional' => true]);
        Measuring::create(['name' => 'п.м.', 'fractional' => true]);

        $this->info('Справочник - guide_v_a_t');
        VAT::create(['name' => 'Без НДС', 'value' => null]);
        VAT::create(['name' => 'НДС 0%', 'value' => 0]);
        VAT::create(['name' => 'НДС 5%', 'value' => 5]);
        VAT::create(['name' => 'НДС 7%', 'value' => 7]);
        VAT::create(['name' => 'НДС 10%', 'value' => 10]);
        VAT::create(['name' => 'НДС 20%', 'value' => 20]);

        $this->info('Справочник - guide_marking_type');
        MarkingType::create(['name' => 'Фототехника']);
        MarkingType::create(['name' => 'Одежда и другие товары лёгкой промышленности']);

        $this->info('Справочник - guide_marking_type');
        Addition::register('Сборка мебели 15%', Addition::ASSEMBLY, false, 15, AssemblyCalculate::class);
        Addition::register('Упаковка товара', Addition::PACKING, false, 1, PackingCalculate::class);
        Addition::register('Подъем на 1 этаж, лестница', Addition::LIFTING, false, 2, LiftingCalculate::class, true);
        Addition::register('Подъем на 1 этаж, лифт', Addition::LIFTING, false, 1, LiftingCalculate::class, true);
        Addition::register('Доставка из Польши', Addition::DELIVERY, false, 15, DeliveryPolandCalculate::class);
        Addition::register('Доставка по городу', Addition::DELIVERY, false, 500);
        Addition::register('Доставка по региону', Addition::DELIVERY, true, 0);
        Addition::register('Доставка по России', Addition::DELIVERY, true, 0);

        return true;
    }
}
