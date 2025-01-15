<?php

namespace App\Modules\Mail\Service;

use App\Modules\Mail\Mailable\AbstractMailable;
use App\Modules\Mail\Entity\SystemMail;
use App\Modules\User\Entity\User;
use Illuminate\Support\Facades\Mail;

class SystemMailService
{

    public function create(AbstractMailable $mailable, int $user_id, array $emails): SystemMail
    {


        return SystemMail::register($mailable, $user_id, $emails);
    }

    public function repeat(SystemMail $mail): void
    {

        $data['html'] = $mail->content;

        Mail::send('mail.repeat', $data, function($message) use ($mail) {
            $message->to($mail->emails, $mail->user->getPublicName())->subject($mail->title);

            foreach($mail->attachments as $file) {
                $message->attach($file);
            }
            $mail->count++;
            $mail->save();
        });
    }

}
