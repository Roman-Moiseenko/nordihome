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
        $result = $this->setCurl($url);
        $this->info(json_encode($result));
        return true;
    }

    private function setCurl($url)
    {
        $headers = [
            "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:42.0) Gecko/20100101 Firefox/42.0",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
            "Cache-Control: max-age=0",
            "Connection: keep-alive",
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);

        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
}
