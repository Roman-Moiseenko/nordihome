<?php
declare(strict_types=1);

namespace App\Modules\Mail\Job;

use App\Modules\Mail\Mailable\OutboxMail;
use App\Modules\Setting\Repository\SettingRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOutbox implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private OutboxMail $mail;

    public function __construct(OutboxMail $mail)
    {
        $this->mail = $mail;
    }

    public function handle()
    {
        try {
            Mail::mailer('outbox')->to($this->mail->outbox->emails)->send($this->mail);
            $this->mail->outbox->send();
        } catch (\Throwable $e) {
            Log::error(json_encode([$e->getMessage(), $e->getLine(), $e->getFile()]));
        }
    }

}
