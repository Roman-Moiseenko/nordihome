<?php
declare(strict_types=1);

namespace App\Modules\Notification\Message;

use App\Modules\Notification\Helpers\NotificationHelper;
use App\Modules\Notification\Helpers\TelegramParams;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use JetBrains\PhpStorm\ExpectedValues;
use NotificationChannels\Telegram\TelegramMessage;


class StaffMessage extends Notification implements ShouldQueue
{

    use Queueable;

    protected int $event;
    protected string $message;
    protected string $url;
    protected TelegramParams|array|null $params;
    private int $type; ///
    private bool $telegram;
    private bool $database;
    //private bool $mail;

    public function __construct(
        #[ExpectedValues(valuesFromClass: NotificationHelper::class)] int $event,
        string $message,
        string $url = '', TelegramParams|array $params = null,
        bool $telegram = true, bool $database = false//, bool $mail = false,
    )
    {
        $this->event = $event;
        $this->message = $message;
        $this->url = $url;
        $this->params = $params;
        $this->telegram = $telegram;
        $this->database = $database;
        //$this->mail = $mail;
    }

    public function via(object $notifiable): array
    {
        $result = [];
        if ($this->telegram) $result[] = 'telegram';
        if ($this->database) $result[] = 'database';
        //if ($this->mail) $result[] = 'mail';
        return $result;
    }

    /**
     * @throws \JsonException
     */
    public function toTelegram(object $notifiable)
    {
        $event = NotificationHelper::EMOJI[$this->event] . ' ' .
            NotificationHelper::EVENTS[$this->event] . ".\n";
        $message = TelegramMessage::create()
            ->content($event)
            ->line($this->message);
        if (!empty($this->params)) {
            if (is_array($this->params)) {
                foreach ($this->params as $param) {
                    $message->buttonWithCallback($param->caption, $param->toTelegram());
                }

            } else {
                $message->buttonWithCallback($this->params->caption, $this->params->toTelegram());
            }
        }

        if (!empty($this->url)) $message = $message->button('Перейти по ссылке', $this->url);

        return $message;
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'code' => $this->event,
            'event' => NotificationHelper::EVENTS[$this->event],
            'message' => $this->message,
            'url' => $this->url,
            'params' => json_encode($this->params),
        ];
    }


}
