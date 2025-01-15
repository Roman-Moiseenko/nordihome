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
    private array $emails;
    private string $systemable_type;
    private int $systemable_id;


    public function __construct(
        User $user,
        AbstractMailable $mail,
        string $systemable_type,
        int $systemable_id,
        array $emails = [])
    {
        $this->user = $user;
        $this->mail = $mail;
        $this->emails = $emails;
        $this->systemable_type = $systemable_type;
        $this->systemable_id = $systemable_id;
    }

    public function handle(SystemMailService $service): void
    {
        if (empty($this->emails)) $this->emails[] = $this->user->email;
        //Сохраняем данные об отправленном письме
        $system_mail = $service->create($this->mail, $this->user->id, $this->emails);
        $system_mail->systemable_type = $this->systemable_type;
        $system_mail->systemable_id = $this->systemable_id;
        $system_mail->save();

        try { //Отправляем письмо
            Mail::mailer('system')->to($this->user->email)->send($this->mail);
        } catch (\Throwable $e) {
            Log::error(json_encode([$e->getMessage(), $e->getLine(), $e->getFile()]));
            $system_mail->notSent(); //Письмо не отправлено, внутрення ошибка
        }
    }
}
