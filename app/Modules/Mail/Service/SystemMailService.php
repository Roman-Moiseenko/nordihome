<?php

namespace App\Modules\Mail\Service;

use App\Modules\Mail\Mailable\AbstractMailable;
use App\Modules\Mail\Entity\SystemMail;
use Illuminate\Support\Facades\Mail;

class SystemMailService
{

    public function create(AbstractMailable $mailable, int $user_id): SystemMail
    {
        return SystemMail::register($mailable, $user_id);
    }

    public function repeat(SystemMail $mail)
    {

        $data['html'] = $mail->content;

        Mail::send('mail.repeat', $data, function($message) use ($mail) {
            $message->to($mail->user->email, $mail->user->getPublicName())->subject($mail->title);

            foreach($mail->attachments as $file) {
                $message->attach($file);
            }
            $mail->count++;
            $mail->save();
        });
    }

}
