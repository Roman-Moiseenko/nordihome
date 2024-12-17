<?php

namespace App\Modules\Mail\Repository;

use App\Modules\Setting\Entity\Mail;
use App\Modules\Setting\Repository\SettingRepository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use App\Modules\Mail\Entity\Inbox;

class InboxRepository
{
    private Mail $mail;

    public function __construct(SettingRepository $settings)
    {
        $this->mail = $settings->getMail();
    }

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Inbox::orderByDesc('created_at');

        $filters = [];

        if ($request->has('from')) {
            $from = $request->string('from')->trim()->value();
            $filters['from'] = $from;
            $query->where(function ($q) use ($from) {
                $q->orWhere('email', 'LIKE', "%$from%")->orWhere('from', 'LIKE', "%$from%");
            });
        }
        if ($request->input('read', 'false') == 'true') {
            $filters['read'] = 'true';
            $query->where('read', false);
        }
        if ($request->has('box')) {
            $box = $request->string('box')->value();
            $filters['box'] = $box;
            $query->where('box', $box);
        }
        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Inbox $inbox) => [
                'id' => $inbox->id,
                'box' => $inbox->box,
                'from' => $inbox->from . ' <' . $inbox->email . '>',
                'subject' => $inbox->subject,
                'attachments' => count($inbox->attachments),
                'created_at' => $inbox->created_at->translatedFormat('j F Y H:i:s'),
                'read_at' => ($inbox->isRead()) ? $inbox->created_at->translatedFormat('j F Y H:i:s') : '',
                'read' => $inbox->isRead(),
            ]);

    }

    /**
     * При нескольких почтовых ящиков добавить данные из Settings (для версии > 1)
     * @return array[]
     */
    public function getBoxes(): array
    {

        return [
            [
                'value' => $this->mail->inbox_name,
                'label' => $this->mail->inbox_name
            ],
        ];
    }
}
