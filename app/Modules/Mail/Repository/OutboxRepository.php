<?php

namespace App\Modules\Mail\Repository;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use App\Modules\Mail\Entity\Outbox;

class OutboxRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Outbox::orderByDesc('created_at');
        $filters = [];

        if ($request->has('email')) {
            $email = $request->string('email')->trim()->value();
            $filters['email'] = $email;
            $query->where('emails', 'LIKE', "%$email%");

        }

        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Outbox $outbox) => [
                'id' => $outbox->id,
                'emails' => $outbox->emails,
                'subject' => $outbox->subject,
                'attachments' => count($outbox->attachments),
                'sent' => $outbox->isSent(),
                'created_at' => $outbox->created_at->translatedFormat('j F Y H:i:s'),
                'sent_at' => is_null($outbox->sent_at) ? '' : $outbox->sent_at->translatedFormat('j F Y H:i:s'),
            ]);

    }
}
