<?php

namespace App\Modules\Mail\Service;

use App\Modules\Setting\Entity\Mail;
use App\Modules\Setting\Repository\SettingRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Modules\Mail\Entity\Inbox;
use Illuminate\Support\Facades\Log;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;

class InboxService
{

    private Mail $mail;

    public function __construct(SettingRepository $settings)
    {
        $this->mail = $settings->getMail();
    }

    public function destroy(Inbox $inbox)
    {
        foreach ($inbox->attachments as $file) {
            $dir = pathinfo(storage_path('app/') . $file, PATHINFO_DIRNAME);
            unlink(storage_path('app/') . $file);
        }
        if (!empty($dir)) rmdir($dir);
        $inbox->delete();
    }

    /**
     * Для версии > 1 когда будет внедрено несколько почтовых ящиков для чтения
     * @return void
     */
    public function readAllInBox(): int
    {
        /*
        $count = 0;
        foreach ($boxes as $box) {
             $count += $this->readInbox($box['name'], $box['password']);
         }
        */
        $count = $this->readInbox($this->mail->inbox_name, $this->mail->inbox_password);
        return $count;
    }

    public function readInbox(string $box, string $password): int
    {
        $cm = new ClientManager();
        $client = $cm->make([
            'host' => env('IMAP_HOST', 'imap.beget.com'),
            'port' => env('IMAP_PORT', 993),
            'encryption' => env('IMAP_ENCRYPTION', 'ssl'),
            'validate_cert' => env('IMAP_VALIDATE_CERT', true),
            'username' => $box . '@' . $this->mail->mail_domain,
            'password' => $password,
            'authentication' => env('IMAP_AUTH', 'OAuth2'),
        ]);

        $client->connect();
        /** @var Folder $folder */
        $folder = $client->getFolder('INBOX');

        //TODO Тест
        // для теста
        //$messages = $folder->query()->markAsRead()->all()->get();
        $messages = $folder->query()->markAsRead()->unseen()->get();

        /** @var Message $message */
        foreach ($messages as $message) {
            $inbox = Inbox::register($box, $message->getFrom()->get()->personal, $message->getFrom()->get()->mail, $message->getSubject());
            $inbox->message = $message->getHTMLBody();

            if ($message->getAttachments()->count() > 0) {
                /** @var Attachment $attachment */
                $path = 'files/inbox/' . $inbox->id . '/';
                mkdir(storage_path('app/') . $path, 0777, true);
                $attach = [];
                foreach ($message->getAttachments() as $attachment) {
                    $attach[$attachment->filename] = $path . $attachment->filename;
                    $attachment->save(storage_path('app/') . $path);
                }
                $inbox->attachments = $attach;
            }
            $inbox->save();
            if ($this->mail->inbox_delete) $message->delete();
        }
        return $messages->count();
    }
}
