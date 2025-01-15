<?php

namespace App\Modules\Notification\Repository;

use App\Modules\Admin\Entity\Admin;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        if (is_null($staff)) throw new \DomainException('Что-то пошло не так');
        $query = $staff->notifications();

        $filters = [];

        if ($request->has('event')) {
            $event = $request->integer('event');
            $filters['event'] = $event;

            $query->where('data', 'LIKE', "%\"code\":\"$event\"%");

        }
        if ($request->input('read', 'false') == 'true' ) {
            $filters['read'] = 'true';
            $query->where('read_at', null);
        }

        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn($notification) => [
                'id' => $notification->id,
                'type' => $notification->type,
                'event' => $notification->data['event'],
                'message' => $notification->data['message'],
                'url' => $notification->data['url'],
                'params' => json_decode($notification->data['params'], true),
                'created_at' => $notification->created_at->translatedFormat('j F Y H:i:s'),
                'read_at' => is_null($notification->read_at) ? null : $notification->read_at->translatedFormat('j F Y H:i:s'),
            ]);


    }
}
