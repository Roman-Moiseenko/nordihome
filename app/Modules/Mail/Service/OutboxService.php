<?php

namespace App\Modules\Mail\Service;

use App\Modules\Mail\Job\SendOutbox;
use App\Modules\Mail\Mailable\OutboxMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Modules\Mail\Entity\Outbox;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class OutboxService
{
    public function create(Request $request): Outbox
    {
        $staff = Auth::guard('admin')->user();
        $outbox = Outbox::register($staff->id,
            $request->input('emails'),
            $request->string('subject')->trim()->value(),
        );

        $this->save_fields($outbox, $request);
        if ($request->boolean('send')) $this->send_mail($outbox);
        return  $outbox;
    }

    public function update(Outbox $outbox, Request $request)
    {
        $outbox->emails = $request->input('emails');
        $outbox->subject = $request->string('subject')->trim()->value();
        $outbox->save();
        $this->save_fields($outbox, $request);

        if ($request->boolean('send')) $this->send_mail($outbox);
    }

    private function save_fields(Outbox $outbox, Request $request)
    {
        $outbox->message = $request->string('message')->trim()->value();
        $files = $request->file('attachments') ?? [];
        $attach = $outbox->attachments;
        foreach ($files as $file) {
            $attach[$file->getClientOriginalName()] = $file->store('files/mail/' . $outbox->id . '/');
        }
        $outbox->attachments = $attach;
        $outbox->save();
    }

    public function send_mail(Outbox $outbox)
    {
        //Отправка почты через очередь
        SendOutbox::dispatch(new OutboxMail($outbox));
    }

    public function destroy(Outbox $outbox)
    {
        if ($outbox->isSent()) throw new \DomainException('Нельзя удалить отправленное письмо!');
        $dir = '';
        foreach ($outbox->attachments as $file) {
            $dir = pathinfo(storage_path('app/') . $file, PATHINFO_DIRNAME);
            unlink(storage_path('app/') . $file);
        }
        if (!empty($dir)) rmdir($dir);
        $outbox->delete();
    }

    public function delete_attachment(Outbox $outbox, Request $request)
    {
        $name = $request->string('file')->value();
        $url = $request->string('url')->value();
        $attach = $outbox->attachments;
        unset($attach[$name]);
        $outbox->attachments = $attach;
        $outbox->save();
        unlink(storage_path('app/') . $url);
    }
}
