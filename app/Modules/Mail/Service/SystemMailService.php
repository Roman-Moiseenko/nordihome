<?php

namespace App\Modules\Mail\Service;

use App\Modules\Mail\Mailable\AbstractMailable;
use App\Modules\Mail\Entity\SystemMail;
use App\Modules\Mail\Mailable\VerifyMail;
use App\Modules\Shared\Application\Interfaces\Mail\MailServiceInterface;
use App\Modules\Shared\Domain\Entities\Mail\Recipient;
use App\Modules\User\Entity\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Modules\Mail\Entity\MailTemplate;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Bus\Queueable;
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

    public function send(MailTemplate $template, array $data, Recipient $recipient): void
    {

        // Создаём стандартное Laravel-письмо «на лету», без отдельных классов
        $mail = new class($template, $data) extends Mailable
        {
            use Queueable, SerializesModels;

            public function __construct(
                private readonly MailTemplate $template,
                private readonly array        $data,
            ) {}

            public function envelope(): Envelope
            {
                return new Envelope(
                    from: new Address(
                        config('mail.from.address'),
                        config('mail.from.name')
                    ),
                    subject: $this->template->subject,
                );
            }

            public function content(): Content
            {
                return new Content(
                    markdown: $this->template->view,
                    with: $this->data,
                );
            }

            public function attachments(): array
            {
                return $this->template->getAttachments($this->data);
            }
        };

        Mail::mailer('system')->to($recipient->email)->send($mail);

        // Логирование в SystemMail для статистики (если нужно)
        if (!is_null($recipient->clientId))
            SystemMail::register($mail, $recipient->clientId, [$recipient->email]);
    }




  /*
        $mail = null;
        if ($templateName == 'auth.verify') $mail = new VerifyMail($data);


        if (is_null($mail)) return;

        $systemMail = SystemMail::register($mail, $recipient->userId, [$recipient->email]);

        if (Mail::mailer('system')->to($recipient->email)->send($mail) == null) {
            Log::error('Письмо не отправлено ' . $recipient->email);
            $systemMail->notSent(); //Письмо не отправлено, внутрення ошибка
        };
*/

}
