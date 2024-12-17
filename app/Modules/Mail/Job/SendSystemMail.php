<?php
declare(strict_types=1);

namespace App\Modules\Mail\Job;

use App\Modules\Mail\Mailable\AbstractMailable;
use App\Modules\Mail\Service\SystemMailService;
use App\Modules\User\Entity\User;
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

    private User $user;
    private AbstractMailable $mail;

    public function __construct(User $user, AbstractMailable $mail)
    {
        $this->user = $user;
        $this->mail = $mail;
    }

    public function handle(SystemMailService $service): void
    {
        //Сохраняем данные об отправленном письме
        $system_mail = $service->create($this->mail, $this->user->id);
        //Отправляем письмо
        try {
            Mail::mailer('system')->to($this->user->email)->send($this->mail);
        } catch (\Throwable $e) {
            Log::error(json_encode([$e->getMessage(), $e->getLine(), $e->getFile()]));
            $system_mail->notSent(); //Письмо не отправлено, внутрення ошибка
        }
    }
}
