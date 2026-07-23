<?php
declare(strict_types=1);

namespace App\Modules\Mail\Job;


use App\Modules\Analytics\LoggerService;
use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Auth\Infrastructure\Models\User;
use App\Modules\Mail\Mailable\AbstractMailable;
use App\Modules\Mail\Service\SystemMailService;
use App\Modules\Order\Entity\Order\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSystemMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Client $client;
    private AbstractMailable $mail;
    private array $emails;
    private string|null $systemable_type;
    private int|null $systemable_id;


    public function __construct(
        Client           $client,
        AbstractMailable $mail,
        string|null          $systemable_type = null,
        int|null         $systemable_id = null,
        array            $emails = [])
    {
        $this->client = $client;
        $this->mail = $mail;
        $this->emails = $emails;
        $this->systemable_type = $systemable_type;
        $this->systemable_id = $systemable_id;
    }

    public function handle(SystemMailService $service, LoggerService $logger): void
    {
        if (empty($this->emails)) $this->emails[] = $this->client->email;
        //Сохраняем данные об отправленном письме
        $system_mail = $service->create($this->mail, $this->client->id, $this->emails);
        $system_mail->systemable_type = $this->systemable_type;
        $system_mail->systemable_id = $this->systemable_id;
        $system_mail->save();

        if ($this->systemable_type == Order::class) {
            $order = Order::find($this->systemable_id);
            $logger->logOrder(order: $order, action: 'Письмо отправлено', value: $this->mail->getName(),
                link: route('admin.mail.system.show', $system_mail));
        }

        try { //Отправляем письмо
            if (Mail::mailer('system')->to($this->client->email)->send($this->mail) == null) {
                Log::error('Письмо не отправлено ' . $this->client->email);
                $system_mail->notSent(); //Письмо не отправлено, внутрення ошибка
            };
        } catch (\Throwable $e) {
            Log::error('Письмо не отправлено - ' . json_encode([$e->getMessage(), $e->getLine(), $e->getFile()]));
            $system_mail->notSent(); //Письмо не отправлено, внутрення ошибка
        }
    }
}
