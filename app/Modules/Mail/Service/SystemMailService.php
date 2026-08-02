<?php

namespace App\Modules\Mail\Service;

use App\Modules\Mail\Mailable\AbstractMailable;
use App\Modules\Mail\Entity\SystemMail;
use App\Modules\Mail\Mailable\VerifyMail;
use App\Modules\Shared\Application\Interfaces\Mail\MailServiceInterface;
use App\Modules\Shared\Domain\Entities\Mail\Recipient;
use App\Modules\User\Entity\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SystemMailService implements MailServiceInterface
{

    public function create(AbstractMailable $mailable, int $user_id, array $emails): SystemMail
    {


        return SystemMail::register($mailable, $user_id, $emails);
    }

    public function repeat(SystemMail $mail): void
    {

        $data['html'] = $mail->content;

        Mail::send('mail.repeat', $data, function($message) use ($mail) {
            $message->to($mail->emails, $mail->client->first_name)->subject($mail->title);

            foreach($mail->attachments as $file) {
                $message->attach($file);
            }
            $mail->count++;
            $mail->save();
        });
    }

    //TODO сделать через useCase
    public function send(string $templateName, array $data, Recipient $recipient): void
    {
        $mail = null;
        if ($templateName == 'auth.verify') $mail = new VerifyMail($data);


        if (is_null($mail)) return;

        $systemMail = SystemMail::register($mail, $recipient->userId, [$recipient->email]);

        if (Mail::mailer('system')->to($recipient->email)->send($mail) == null) {
            Log::error('Письмо не отправлено ' . $recipient->email);
            $systemMail->notSent(); //Письмо не отправлено, внутрення ошибка
        };

    }
}
