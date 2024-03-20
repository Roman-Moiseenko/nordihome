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

class DelWebHookCommand extends Command
{
    protected $signature = 'tm:del-webhook';

    protected $description = 'Удаление вебхука';

    public function handle(): bool
    {
        $token = env('TELEGRAM_BOT_TOKEN', null);
        $url = 'https://api.telegram.org/bot' . $token . '/setWebhook?url=';
        $result = file_get_contents($url);
        $this->info(json_encode($result));
        return true;
    }
}
