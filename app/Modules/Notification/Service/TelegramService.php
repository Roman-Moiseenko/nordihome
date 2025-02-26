<?php
declare(strict_types=1);

namespace App\Modules\Notification\Service;

use App\Modules\Notification\Events\TelegramHasReceived;
use App\Modules\Notification\Repository\TelegramRepository;
use App\Modules\Setting\Entity\Notification;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Repository\SettingRepository;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Telegram\TelegramUpdates;

class TelegramService
{

    private Notification $notification;
    private TelegramRepository $repository;

    public function __construct(Settings $settings, TelegramRepository $repository)
    {
        $this->notification = $settings->notification;
        $this->repository = $repository;
    }

    public function getListChatIds(): array
    {
        //if (config('app.env') != 'local')
           // $this->delWebHook();
        $updates = TelegramUpdates::create()->limit(2)
            ->options([
                'timeout' => 0,
            ])->get();
        $list = [];

        if($updates['ok']) {
            foreach ($updates['result'] as $user) {
                $list[] = [
                    'name' => $user['message']['chat']['first_name'],
                    'login' => $user['message']['chat']['username'],
                    'id' => $user['message']['chat']['id'],
                ];
            }
        } else {
            return $updates;
        }
        //if (config('app.env') != 'local')
          //  $this->setWebHook();
        return $list;
    }

    public function setWebHook(): bool|string
    {
        $route = route('api.telegram.web-hook');
        $url = "https://api.telegram.org/bot" .
            $this->notification->telegram_api .
            "/setWebhook?url=" . $route . '&certificate=@crm.pem';
        return $this->setCurl($url);
    }

    public function delWebHook(): bool|string
    {
        $url = 'https://api.telegram.org/bot' .
            $this->notification->telegram_api
            . '/setWebhook?url=';
        return $this->setCurl($url);
    }

    public function getWebHook(): bool|string
    {
        $url = 'https://api.telegram.org/bot' .
            $this->notification->telegram_api
            . '/getWebhookInfo';
        return $this->setCurl($url);
    }

    public function checkOperation(mixed $callback)
    {
        $message_id = $callback['message']['message_id'];
        $message = $callback['message']['text'];
        $telegram_user_id = $callback['from']['id'];

        $user = $this->repository->getUserByTelegram($telegram_user_id);
        $this->repository->checkMessage($message_id, $telegram_user_id, $message);//Проверка Отвечал ли user на message_id
        $data = json_decode($callback['data'], true);

        event(new TelegramHasReceived(
            $user, (int)$data['operation'], (int)$data['id']
        ));
    }

    private function setCurl($url): bool|string
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

    public function getMessage(mixed $message): void
    {
        $id = $message['from']['id'];
        if (isset($message['entities'])) {
            if ($message['entities'][0]['type'] == 'bot_command') {
                //TODO Обработка команды от бота
                $command  = $message['text'];

                \Log::info($command);
                if ($command == '/help') {
                    $message = TelegramMessage::create()
                        ->content('Меню:')
                        ->button('Доставка и Оплата', 'https://nbrussia.ru/page/delivery')
                        ->button('Обмен и Возврат', 'https://nbrussia.ru/page/refund')
                        ->button('Информация', 'https://nbrussia.ru/page/information')
                        ->button('Как выбрать размер', 'https://nbrussia.ru/page/size');

                    $message->to($id)->send();
                }

            }
        } else {

            $message = TelegramMessage::create()
                ->content('Привет!');;
            $message->to($id)->send();
        }

    }
}
