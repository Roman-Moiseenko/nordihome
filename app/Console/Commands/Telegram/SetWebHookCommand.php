<?php
declare(strict_types=1);

namespace App\Console\Commands\Telegram;

use App\Modules\Accounting\Service\MovementService;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Service\ReserveService;
use App\Modules\Order\Service\SalesService;
use Illuminate\Console\Command;

class SetWebHookCommand extends Command
{
    protected $signature = 'tm:webhook';

    protected $description = 'Установка вебхука';

    public function handle(): bool
    {
        $token = env('TELEGRAM_BOT_TOKEN', null);
        $route = route('api.telegram');
        $url = 'https://api.telegram.org/bot' . $token . '/setWebhook?url=' . $route;
        $result = file_get_contents($url);
        $this->info(json_encode($result));
        return true;
    }
}
