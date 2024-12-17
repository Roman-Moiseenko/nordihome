<?php

namespace App\Modules\Mail\Repository;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use App\Modules\Mail\Entity\SystemMail;

class SystemMailRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = SystemMail::orderByDesc('created_at');
        $filters =[];

        if ($request->has('mailable')) {
            $mailable = $request->string('mailable')->trim()->value();
            $filters['mailable'] = $mailable;
            $query->where('mailable', $mailable);
        }

        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(SystemMail $system) => $this->SystemMailToArray($system));
    }

    public function SystemMailToArray(SystemMail $system): array
    {
        return [
            'id' => $system->id,
            'mailable' => $system->getMailable(),
            'created_at' => $system->created_at->translatedFormat('j F Y H:i:s'),
            'updated_at' => $system->updated_at->translatedFormat('j F Y H:i:s'),
            'user' => $system->user->getPublicName(),
            'title' => $system->title,
            'content' => $system->content,
            'attachments' => $system->attachments,
            'count_attachments' => count($system->attachments),
            'count' => $system->count,
        ];
    }
}
